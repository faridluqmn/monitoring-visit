<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VisitController extends Controller
{
    /**
     * Display the fullscreen map dashboard with all visits.
     */
    public function index(): View
    {
        $visits = Visit::query()
            ->orderByDesc('created_at')
            ->get();

        $officers = Visit::query()
            ->whereNotNull('visitor_name')
            ->where('visitor_name', '!=', '')
            ->distinct()
            ->orderBy('visitor_name')
            ->pluck('visitor_name');

        return view('visit.index', [
            'visits' => $visits,
            'officers' => $officers,
        ]);
    }

    /**
     * Show the mobile-friendly form for recording a field visit.
     */
    public function create(): View
    {
        $officers = Visit::query()
            ->whereNotNull('visitor_name')
            ->where('visitor_name', '!=', '')
            ->distinct()
            ->orderBy('visitor_name')
            ->pluck('visitor_name');

        return view('visit.create', [
            'officers' => $officers,
        ]);
    }

    /**
     * Store a new visit, uploading the photo to Supabase Storage and saving data to PostgreSQL.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'visitor_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['required', 'file', 'image', 'max:15360'],
        ], [
            'visitor_name.required' => 'Nama petugas wajib diisi.',
            'latitude.required' => 'Titik koordinat latitude belum didapatkan dari GPS.',
            'longitude.required' => 'Titik koordinat longitude belum didapatkan dari GPS.',
            'photo.required' => 'Foto bukti kunjungan wajib diambil.',
            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 15MB.',
        ]);

        $photo = $request->file('photo');
        $extension = $photo->getClientOriginalExtension() ?: 'jpg';
        $filename = 'visit_'.time().'_'.Str::random(10).'.'.$extension;
        $storagePath = 'visits/'.date('Y/m').'/'.$filename;

        $supabaseUrl = rtrim((string) config('services.supabase.url'), '/');
        $supabaseKey = (string) config('services.supabase.key');
        $bucket = (string) config('services.supabase.bucket', 'visit-photos');

        if (! empty($supabaseUrl) && ! empty($supabaseKey)) {
            $endpoint = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$storagePath}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$supabaseKey}",
                'apikey' => $supabaseKey,
            ])->withBody(
                file_get_contents($photo->getRealPath()),
                $photo->getMimeType() ?: 'image/jpeg'
            )->post($endpoint);

            if (! $response->successful()) {
                Log::error('Supabase Storage upload failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah foto ke Supabase Storage: '.($response->json('message') ?? $response->body()),
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->withErrors(['photo' => 'Gagal mengunggah foto ke Supabase Storage: '.($response->json('message') ?? $response->body())]);
            }

            $photoUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$storagePath}";
        } else {
            // Development fallback if Supabase credentials are not set in .env
            $savedPath = $photo->store('visit-photos', 'public');
            $photoUrl = asset('storage/'.$savedPath);
        }

        $visit = Visit::create([
            'visitor_name' => $validated['visitor_name'],
            'notes' => $validated['notes'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'photo_url' => $photoUrl,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kunjungan lapangan berhasil disimpan.',
                'data' => $visit,
                'redirect' => route('visits.index'),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Data kunjungan lapangan berhasil disimpan!');
    }
}
