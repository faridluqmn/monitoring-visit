<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Kunjungan Lapangan | Field Visit Tracking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet.js CSS for Interactive Pinning -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.05); }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-brand-500 selection:text-white pb-10">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-md shadow-brand-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">Field Visit</h1>
                    <p class="text-xs font-medium text-slate-500">Tracking & Mapping</p>
                </div>
            </div>

            <a href="{{ route('visits.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span>Lihat Peta</span>
            </a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-xl mx-auto px-4 pt-5">

        <!-- Flash Messages (Session) -->
        @if(session('success'))
            <div id="flash-success" class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm">
                    <p class="font-semibold">{{ session('success') }}</p>
                    <a href="{{ route('visits.index') }}" class="mt-1 inline-block text-xs font-bold text-emerald-700 underline hover:text-emerald-900">
                        Buka Dashboard Peta &rarr;
                    </a>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs">
                <div class="flex items-center gap-2 font-semibold text-sm text-rose-900 mb-1">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Mohon periksa kembali form:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 pl-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Form -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden">
            <!-- Header Card Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold mb-2 backdrop-blur-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Presensi Lapangan
                </div>
                <h2 class="text-xl font-extrabold text-white tracking-tight">Check-In Lapangan</h2>
                <p class="text-xs text-slate-300 mt-1">Lengkapi nama petugas, swafoto kehadiran, dan verifikasi titik koordinat kunjungan.</p>
            </div>

            <!-- The Form -->
            <form id="visitForm" action="{{ request()->isSecure() || app()->environment('production') || env('VERCEL') || str_contains(request()->getHost(), 'vercel.app') ? secure_url(route('visits.store', [], false)) : route('visits.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <!-- Hidden Latitude and Longitude -->
                <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude') }}">

                @php
                    // Pastikan daftar nama petugas unik dan rapi, jika kosong siapkan nama default agar tampilan langsung menarik
                    $allOfficers = ($officers ?? collect())->filter()->unique()->values();
                    if ($allOfficers->isEmpty()) {
                        $allOfficers = collect(['Farid', 'Dafa', 'Fira']);
                    }
                @endphp

                <!-- Input: Nama Petugas (Modern Custom Mobile UI) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="visitor_name" class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            <span>Nama Petugas</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full border border-brand-200/60">
                            Pilih / Ketik Nama
                        </span>
                    </div>

                    <!-- Quick Pick Chips for Mobile (Sentuh Sekali untuk Memilih) -->
                    <div class="mb-2.5">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-semibold text-slate-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>Pilih Cepat Petugas:</span>
                            </span>
                            <span class="text-[10px] text-slate-400">Ketuk untuk memilih</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5" id="quickOfficerChips">
                            @foreach($allOfficers as $officer)
                                <button type="button"
                                        onclick="selectOfficer('{{ addslashes($officer) }}', this)"
                                        class="officer-quick-chip group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200 hover:border-brand-300 shadow-2xs transition-all duration-150 active:scale-95 cursor-pointer"
                                        data-officer="{{ strtolower($officer) }}">
                                    <span class="w-5 h-5 rounded-lg bg-gradient-to-tr from-brand-600 to-indigo-500 text-white font-bold text-[10px] flex items-center justify-center shadow-2xs group-hover:scale-105 transition">
                                        {{ strtoupper(substr($officer, 0, 1)) }}
                                    </span>
                                    <span class="chip-name">{{ $officer }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom Styled Modern Input Container -->
                    <div class="relative" id="officerComboboxContainer">
                        <div class="relative flex items-center bg-white rounded-2xl border-2 border-slate-200 hover:border-brand-300 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition duration-200 shadow-2xs overflow-hidden">
                            <!-- Dynamic Avatar Badge -->
                            <div class="pl-3.5 pr-1.5 flex items-center pointer-events-none shrink-0">
                                <div id="officerAvatarBadge" class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Text Input -->
                            <input type="text"
                                   name="visitor_name"
                                   id="visitor_name"
                                   required
                                   placeholder="Ketik atau pilih nama petugas..."
                                   value="{{ old('visitor_name') }}"
                                   autocomplete="off"
                                   class="w-full py-3.5 px-2 bg-transparent text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none">

                            <!-- Clear Button -->
                            <button type="button"
                                    id="clearOfficerBtn"
                                    onclick="clearOfficerInput()"
                                    class="hidden p-1.5 mr-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                    title="Hapus nama">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Dropdown Trigger Button -->
                            <button type="button"
                                    id="officerDropdownToggle"
                                    onclick="toggleOfficerDropdown()"
                                    class="px-3.5 py-3.5 text-xs font-bold text-slate-600 hover:text-brand-600 bg-slate-50 hover:bg-slate-100 border-l border-slate-200 flex items-center gap-1.5 transition cursor-pointer shrink-0">
                                <span>Daftar</span>
                                <svg id="officerChevronIcon" class="w-4 h-4 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Dropdown Menu / Modern Floating Card -->
                        <div id="officerDropdownMenu"
                             class="hidden absolute left-0 right-0 z-40 mt-2 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 animate-in fade-in zoom-in-95 duration-150">
                            <div class="p-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Pilih Petugas Tersimpan</span>
                                </span>
                                <span class="text-[10px] text-slate-400">{{ count($allOfficers) }} Terdaftar</span>
                            </div>

                            <div id="officersList" class="max-h-56 overflow-y-auto p-1.5 space-y-1">
                                @foreach($allOfficers as $officer)
                                    <div class="officer-option flex items-center justify-between p-2 rounded-xl hover:bg-brand-50/80 cursor-pointer text-xs transition group"
                                         data-name="{{ strtolower($officer) }}"
                                         data-raw="{{ $officer }}"
                                         onclick="selectOfficer('{{ addslashes($officer) }}')">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 text-white font-extrabold flex items-center justify-center shrink-0 text-xs shadow-2xs">
                                                {{ strtoupper(substr($officer, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 group-hover:text-brand-700 truncate text-xs">
                                                    {{ $officer }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">Petugas Presensi</p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-semibold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition">
                                            Pilih &rarr;
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div id="newOfficerNotice" class="hidden p-3 bg-brand-50/60 text-xs border-t border-brand-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-slate-700 min-w-0">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="truncate">Gunakan nama baru: <strong id="newOfficerNameText" class="text-brand-700 font-bold"></strong></span>
                                    </div>
                                    <button type="button"
                                            onclick="toggleOfficerDropdown(false)"
                                            class="text-[11px] font-bold text-brand-600 hover:text-brand-800 shrink-0 ml-2">
                                        Gunakan &crarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input: Keterangan Kegiatan -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <span>Keterangan Kegiatan</span>
                        </label>
                        <span id="charCount" class="text-[10px] text-slate-400 font-medium">0/500</span>
                    </div>

                    <!-- Quick Activity Presets for Mobile -->
                    <div class="mb-2 flex flex-wrap gap-1.5">
                        <button type="button" onclick="appendNote('Magang')" class="preset-note-btn px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 border border-slate-200/80 transition active:scale-95 cursor-pointer">
                            🏢 Magang
                        </button>
                        <button type="button" onclick="appendNote('Kunjungan Rutin')" class="preset-note-btn px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 border border-slate-200/80 transition active:scale-95 cursor-pointer">
                            📋 Kunjungan Rutin
                        </button>
                        <button type="button" onclick="appendNote('Verifikasi Lapangan')" class="preset-note-btn px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 border border-slate-200/80 transition active:scale-95 cursor-pointer">
                            🔍 Verifikasi Lapangan
                        </button>
                        <button type="button" onclick="appendNote('Monitoring Titik')" class="preset-note-btn px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 border border-slate-200/80 transition active:scale-95 cursor-pointer">
                            📍 Monitoring Titik
                        </button>
                    </div>

                    <div class="relative rounded-2xl border-2 border-slate-200 hover:border-brand-300 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition duration-200 shadow-2xs overflow-hidden bg-white">
                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  maxlength="500"
                                  oninput="document.getElementById('charCount').textContent = this.value.length + '/500'"
                                  placeholder="Catatan temuan atau tujuan kegiatan kunjungan di lapangan..."
                                  class="block w-full p-3.5 bg-transparent text-slate-900 placeholder:text-slate-400 text-xs font-medium focus:outline-none resize-none leading-relaxed">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Input: Foto Kamera Selfie -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Foto Bukti Lapangan <span class="text-rose-500">*</span>
                    </label>

                    <!-- Native hidden file inputs -->
                    <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden">
                    <input type="file" id="galleryInput" accept="image/*" class="hidden">
                    <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden" required>

                    <!-- Custom Camera Upload Dropzone / Button for Android & Mobile -->
                    <div id="dropzoneArea"
                         class="border-2 border-dashed border-slate-300 hover:border-brand-500 bg-slate-50/70 hover:bg-brand-50/30 rounded-2xl p-4 sm:p-6 text-center transition flex flex-col items-center justify-center">
                        
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white shadow-xs border border-slate-200 flex items-center justify-center text-brand-600 mb-2.5">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>

                        <p class="text-sm font-bold text-slate-800">
                            Foto Bukti Lapangan
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5 max-w-xs mb-3.5">
                            Ambil langsung lewat kamera HP atau pilih file foto dari galeri.
                        </p>

                        <!-- Action Buttons for Android & Touch Devices -->
                        <div class="grid grid-cols-2 gap-2.5 w-full max-w-xs">
                            <button type="button"
                                    onclick="document.getElementById('cameraInput').click()"
                                    class="py-2.5 px-3 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                                <span>Kamera</span>
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('galleryInput').click()"
                                    class="py-2.5 px-3 bg-white hover:bg-slate-100 active:scale-95 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Galeri HP</span>
                            </button>
                        </div>
                    </div>

                    <!-- Image Preview Box (Clean & Professional) -->
                    <div id="previewCard" class="hidden mt-3 bg-slate-50 border border-slate-200 rounded-2xl p-3 sm:p-3.5 space-y-3">
                        <div class="relative rounded-xl overflow-hidden bg-black/5 aspect-4/3 flex items-center justify-center border border-slate-200">
                            <img id="previewImage" src="" alt="Preview Foto Lapangan" class="w-full h-full object-cover">
                            <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1.5">
                                <button type="button"
                                        onclick="document.getElementById('cameraInput').click()"
                                        class="bg-slate-900/85 hover:bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1.5 rounded-xl backdrop-blur-sm shadow flex items-center gap-1 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    </svg>
                                    Foto Ulang
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('galleryInput').click()"
                                        class="bg-slate-900/85 hover:bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1.5 rounded-xl backdrop-blur-sm shadow flex items-center gap-1 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Galeri
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GPS Location Section -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Titik Lokasi GPS <span class="text-rose-500">*</span>
                        </label>
                        <button type="button"
                                onclick="fetchCurrentLocation()"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-2.5 py-1 rounded-lg transition">
                            <svg id="refreshGpsIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Perbarui GPS</span>
                        </button>
                    </div>

                    <!-- GPS Status Card -->
                    <div id="gpsCard" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 transition">
                        <!-- Searching State -->
                        <div id="gpsSearching" class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Mendeteksi Titik GPS...</p>
                                <p class="text-xs text-slate-500">Mohon izinkan akses lokasi di browser perangkat Anda.</p>
                            </div>
                        </div>

                        <!-- Success State (Locked) -->
                        <div id="gpsSuccess" class="hidden">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-xs font-bold text-emerald-800">Lokasi GPS Terverifikasi</span>
                                </div>
                                <span id="gpsAccuracy" class="text-[11px] font-semibold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                    Akurasi ± 5m
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-3">
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Latitude</span>
                                    <span id="displayLat" class="text-xs font-mono font-bold text-slate-800">-</span>
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Longitude</span>
                                    <span id="displayLng" class="text-xs font-mono font-bold text-slate-800">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Error State -->
                        <div id="gpsError" class="hidden flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="text-xs">
                                <p class="font-bold text-rose-800 text-sm">Gagal Mengakses GPS</p>
                                <p id="gpsErrorMessage" class="text-rose-600 mt-0.5">Akses lokasi tidak tersedia. Anda dapat menentukan titik manual menggunakan peta di bawah.</p>
                                <button type="button"
                                        onclick="fetchCurrentLocation()"
                                        class="mt-2 text-xs font-bold text-rose-700 underline hover:text-rose-900">
                                    Coba Deteksi Ulang &rarr;
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Mini-Map for Verifying & Pinpointing Location -->
                    <div class="mt-3.5 bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <div class="p-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-slate-800">Peta Verifikasi Lokasi</span>
                            </div>
                        </div>

                        <!-- Quick Search Bar for Office/Street/Place in Indonesia -->
                        <div class="p-2.5 border-b border-slate-100 bg-white flex gap-1.5">
                            <div class="relative flex-1">
                                <input type="text"
                                       id="mapSearchInput"
                                       placeholder="Cari nama kantor / jalan / tempat (cth: Balai Kota Surabaya)..."
                                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); searchLocationOnMap(); }"
                                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button type="button"
                                    id="mapSearchBtn"
                                    onclick="searchLocationOnMap()"
                                    class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0 cursor-pointer shadow-xs">
                                <span id="mapSearchText">Cari</span>
                            </button>
                        </div>

                        <!-- Leaflet Map Container -->
                        <div id="miniMap" class="w-full h-64 z-10 bg-slate-100"></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            id="submitBtn"
                            class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 active:scale-[0.99] text-white font-bold text-sm shadow-lg shadow-brand-500/30 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg id="submitIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submitText">Simpan Kunjungan Lapangan</span>
                    </button>
                    <p class="text-[11px] text-center text-slate-400 mt-2">
                        Data dan foto akan disimpan aman ke PostgreSQL & Supabase Storage
                    </p>
                </div>
            </form>
        </div>

    </main>

    <!-- Success Modal Popup -->
    <div id="successModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 transform transition-all scale-95 opacity-0 duration-200" id="successModalBox">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold text-slate-900">Kunjungan Berhasil Disimpan!</h3>
            <p id="successModalMessage" class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Foto selfie telah diunggah ke Supabase Storage dan titik koordinat GPS telah tersimpan.
            </p>
            <div class="mt-6 space-y-2.5">
                <a href="{{ route('visits.index') }}" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-md shadow-brand-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Lihat di Dashboard Peta
                </a>
                <button type="button" onclick="resetFormAndCloseModal()" class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    + Input Kunjungan Lainnya
                </button>
            </div>
        </div>
    </div>

    <!-- Leaflet.js CDN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- JavaScript: GPS Location, Leaflet Mini-Map & HTML5 Canvas Image Compression -->
    <script>
        // State
        let rawPhotoFile = null;
        let compressedPhotoFile = null;
        let isGpsLocked = false;
        let miniMap = null;
        let miniMarker = null;

        // Batas Wilayah Negara Kesatuan Republik Indonesia (NKRI)
        const indonesiaBounds = L.latLngBounds(
            L.latLng(-11.5, 94.5), // Barat Daya
            L.latLng(6.5, 141.5)   // Timur Laut
        );

        // Elements
        const visitForm = document.getElementById('visitForm');
        const photoInput = document.getElementById('photoInput');
        const dropzoneArea = document.getElementById('dropzoneArea');
        const previewCard = document.getElementById('previewCard');
        const previewImage = document.getElementById('previewImage');
        const statOriginalSize = document.getElementById('statOriginalSize');
        const statCompressedSize = document.getElementById('statCompressedSize');
        const statSavingsPercent = document.getElementById('statSavingsPercent');

        // Officers Custom Combobox & Quick Chips
        const visitorNameInput = document.getElementById('visitor_name');
        const officerDropdownMenu = document.getElementById('officerDropdownMenu');
        const officerChevronIcon = document.getElementById('officerChevronIcon');
        const officerComboboxContainer = document.getElementById('officerComboboxContainer');
        const officerOptions = document.querySelectorAll('.officer-option');
        const officerAvatarBadge = document.getElementById('officerAvatarBadge');
        const clearOfficerBtn = document.getElementById('clearOfficerBtn');
        const quickOfficerChips = document.querySelectorAll('.officer-quick-chip');

        function updateOfficerUI(name) {
            const trimmed = (name || '').trim();
            if (trimmed) {
                if (clearOfficerBtn) clearOfficerBtn.classList.remove('hidden');
                if (officerAvatarBadge) {
                    officerAvatarBadge.innerHTML = `<span class="w-full h-full rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 text-white font-extrabold flex items-center justify-center text-xs shadow-2xs">${trimmed.charAt(0).toUpperCase()}</span>`;
                }
            } else {
                if (clearOfficerBtn) clearOfficerBtn.classList.add('hidden');
                if (officerAvatarBadge) {
                    officerAvatarBadge.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`;
                }
            }

            // Sync visual active state on quick chips
            const lowerName = trimmed.toLowerCase();
            quickOfficerChips.forEach(chip => {
                const chipOfficer = chip.getAttribute('data-officer');
                if (chipOfficer && chipOfficer === lowerName) {
                    chip.classList.add('bg-brand-600', 'text-white', 'ring-2', 'ring-brand-400', 'shadow-xs');
                    chip.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                } else {
                    chip.classList.remove('bg-brand-600', 'text-white', 'ring-2', 'ring-brand-400', 'shadow-xs');
                    chip.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
                }
            });
        }

        function toggleOfficerDropdown(forceState) {
            if (!officerDropdownMenu) return;
            const isClosed = officerDropdownMenu.classList.contains('hidden');
            const shouldOpen = forceState !== undefined ? forceState : isClosed;

            if (shouldOpen) {
                filterOfficerOptions(visitorNameInput.value);
                officerDropdownMenu.classList.remove('hidden');
                if (officerChevronIcon) officerChevronIcon.classList.add('rotate-180');
            } else {
                officerDropdownMenu.classList.add('hidden');
                if (officerChevronIcon) officerChevronIcon.classList.remove('rotate-180');
            }
        }

        function filterOfficerOptions(query) {
            const q = (query || '').toLowerCase().trim();
            let matches = 0;

            officerOptions.forEach(opt => {
                const name = opt.getAttribute('data-name');
                if (!q || name.includes(q)) {
                    opt.classList.remove('hidden');
                    matches++;
                } else {
                    opt.classList.add('hidden');
                }
            });

            const newNotice = document.getElementById('newOfficerNotice');
            const newNameText = document.getElementById('newOfficerNameText');
            if (newNotice && newNameText) {
                if (q && matches === 0) {
                    newNameText.textContent = query;
                    newNotice.classList.remove('hidden');
                } else {
                    newNotice.classList.add('hidden');
                }
            }
        }

        function selectOfficer(name, clickedEl) {
            if (visitorNameInput) {
                visitorNameInput.value = name;
                updateOfficerUI(name);
            }
            toggleOfficerDropdown(false);
        }

        function clearOfficerInput() {
            if (visitorNameInput) {
                visitorNameInput.value = '';
                updateOfficerUI('');
                visitorNameInput.focus();
            }
        }

        function appendNote(text) {
            const notesEl = document.getElementById('notes');
            if (!notesEl) return;
            const current = notesEl.value.trim();
            if (!current) {
                notesEl.value = text;
            } else if (!current.includes(text)) {
                notesEl.value = current + ' - ' + text;
            }
            const countEl = document.getElementById('charCount');
            if (countEl) countEl.textContent = notesEl.value.length + '/500';
            notesEl.focus();
        }

        if (visitorNameInput) {
            // Inisialisasi awal saat halaman dimuat jika old('visitor_name') terisi
            if (visitorNameInput.value) {
                updateOfficerUI(visitorNameInput.value);
            }

            visitorNameInput.addEventListener('focus', () => {
                toggleOfficerDropdown(true);
            });
            visitorNameInput.addEventListener('input', (e) => {
                filterOfficerOptions(e.target.value);
                updateOfficerUI(e.target.value);
                toggleOfficerDropdown(true);
            });
            visitorNameInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    toggleOfficerDropdown(false);
                } else if (e.key === 'Enter') {
                    const firstVisible = Array.from(officerOptions).find(o => !o.classList.contains('hidden'));
                    if (firstVisible && !officerDropdownMenu.classList.contains('hidden')) {
                        e.preventDefault();
                        selectOfficer(firstVisible.getAttribute('data-raw'));
                    }
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (officerComboboxContainer && !officerComboboxContainer.contains(e.target)) {
                toggleOfficerDropdown(false);
            }
        });

        const latitudeInput = document.getElementById('latitudeInput');
        const longitudeInput = document.getElementById('longitudeInput');
        const displayLat = document.getElementById('displayLat');
        const displayLng = document.getElementById('displayLng');
        const gpsAccuracy = document.getElementById('gpsAccuracy');
        const gpsSearching = document.getElementById('gpsSearching');
        const gpsSuccess = document.getElementById('gpsSuccess');
        const gpsError = document.getElementById('gpsError');
        const gpsErrorMessage = document.getElementById('gpsErrorMessage');
        const refreshGpsIcon = document.getElementById('refreshGpsIcon');

        const submitBtn = document.getElementById('submitBtn');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');

        const successModal = document.getElementById('successModal');
        const successModalBox = document.getElementById('successModalBox');

        // Helper: Format Bytes to KB/MB
        function formatBytes(bytes, decimals = 1) {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }

        // 1. Inisialisasi Mini-Map Interaktif
        function initMiniMap() {
            if (miniMap) return;

            // Default center: jika sudah ada nilai koordinat, gunakan itu. Jika belum, titik tengah Indonesia
            const hasInitialCoords = latitudeInput.value && longitudeInput.value;
            const initialLat = hasInitialCoords ? parseFloat(latitudeInput.value) : -2.5489;
            const initialLng = hasInitialCoords ? parseFloat(longitudeInput.value) : 118.0149;
            const initialZoom = hasInitialCoords ? 16 : 5;

            miniMap = L.map('miniMap', {
                maxBounds: indonesiaBounds,
                maxBoundsViscosity: 1.0,
                minZoom: 4,
                maxZoom: 19
            }).setView([initialLat, initialLng], initialZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                bounds: indonesiaBounds,
                attribution: '&copy; OpenStreetMap'
            }).addTo(miniMap);

            const redPin = L.divIcon({
                className: 'custom-pin-marker',
                iconSize: [36, 44],
                iconAnchor: [18, 42],
                popupAnchor: [0, -40],
                html: `
                    <div style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
                        <svg width="36" height="44" viewBox="0 0 44 52" fill="none">
                            <path d="M22 0C9.84974 0 0 9.84974 0 22C0 35.5 19.5 50.5 21.1 51.7C21.6 52.1 22.4 52.1 22.9 51.7C24.5 50.5 44 35.5 44 22C44 9.84974 34.1503 0 22 0Z" fill="#DC2626"/>
                            <circle cx="22" cy="20" r="10" fill="white"/>
                        </svg>
                    </div>
                `
            });

            miniMarker = L.marker([initialLat, initialLng], {
                icon: redPin,
                draggable: true
            }).addTo(miniMap);

            miniMarker.bindPopup('<b>Titik Lokasi Kunjungan</b>');

            // Saat pin digeser
            miniMarker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                updateCoordinates(pos.lat, pos.lng);
            });

            // Saat peta diklik langsung
            miniMap.on('click', function (e) {
                miniMarker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });
        }

        // Helper untuk update koordinat ke input form dan tampilan UI
        function updateCoordinates(lat, lng, labelAccuracy) {
            latitudeInput.value = lat;
            longitudeInput.value = lng;
            displayLat.textContent = Number(lat).toFixed(6);
            displayLng.textContent = Number(lng).toFixed(6);

            if (labelAccuracy) {
                gpsAccuracy.textContent = labelAccuracy;
            }

            isGpsLocked = true;
            gpsSearching.classList.add('hidden');
            gpsError.classList.add('hidden');
            gpsSuccess.classList.remove('hidden');
        }

        // 2. Pencarian Alamat / Tempat di Indonesia
        async function searchLocationOnMap() {
            const query = document.getElementById('mapSearchInput').value.trim();
            if (!query) return;

            const btn = document.getElementById('mapSearchBtn');
            const txt = document.getElementById('mapSearchText');
            txt.textContent = 'Mencari...';
            btn.disabled = true;

            try {
                // Query Nominatim OpenStreetMap dibatasi khusus negara Indonesia (countrycodes=id)
                const url = `https://nominatim.openstreetmap.org/search?format=json&countrycodes=id&limit=1&q=${encodeURIComponent(query)}`;
                const response = await fetch(url);
                const results = await response.json();

                if (results && results.length > 0) {
                    const lat = parseFloat(results[0].lat);
                    const lon = parseFloat(results[0].lon);

                    if (miniMap && miniMarker) {
                        miniMarker.setLatLng([lat, lon]);
                        miniMap.setView([lat, lon], 17);
                        miniMarker.openPopup();
                    }

                    const shortName = results[0].display_name.split(',')[0];
                    updateCoordinates(lat, lon, `Pencarian: ${shortName}`);
                } else {
                    alert('Lokasi tidak ditemukan di Indonesia. Coba ketik nama gedung, jalan, atau kelurahan/kecamatan yang lebih spesifik.');
                }
            } catch (err) {
                console.error(err);
                alert('Pencarian sedang sibuk. Silakan geser pin merah secara manual pada peta.');
            } finally {
                txt.textContent = 'Cari';
                btn.disabled = false;
            }
        }

        // 3. Geolocation Browser Handler
        function fetchCurrentLocation() {
            gpsSearching.classList.remove('hidden');
            gpsSuccess.classList.add('hidden');
            gpsError.classList.add('hidden');
            refreshGpsIcon.classList.add('animate-spin');

            if (!navigator.geolocation) {
                showGpsError('Browser tidak mendukung geolokasi GPS.');
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            };

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const acc = Math.round(position.coords.accuracy);

                    // Beri label pembeda jika di laptop (akurasi biasanya > 100 meter)
                    const label = acc > 150 ? `IP/Wi-Fi (± ${acc}m)` : `GPS (± ${acc}m)`;
                    updateCoordinates(lat, lng, label);

                    if (miniMap && miniMarker) {
                        miniMarker.setLatLng([lat, lng]);
                        miniMap.setView([lat, lng], 16);
                        miniMarker.openPopup();
                    }

                    refreshGpsIcon.classList.remove('animate-spin');
                },
                (error) => {
                    refreshGpsIcon.classList.remove('animate-spin');
                    let message = 'Gagal mendeteksi lokasi.';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Izin lokasi browser ditolak. Anda tetap bisa menentukan lokasi dengan menggeser pin di peta.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Sinyal lokasi tidak tersedia. Silakan geser pin pada peta.';
                            break;
                        case error.TIMEOUT:
                            message = 'Waktu deteksi GPS habis. Silakan geser pin pada peta.';
                            break;
                    }
                    showGpsError(message);
                },
                options
            );
        }

        function showGpsError(msg) {
            gpsSearching.classList.add('hidden');
            gpsSuccess.classList.add('hidden');
            gpsError.classList.remove('hidden');
            gpsErrorMessage.textContent = msg;
            refreshGpsIcon.classList.remove('animate-spin');
            isGpsLocked = false;
        }

        // Run on Load
        document.addEventListener('DOMContentLoaded', () => {
            initMiniMap();
            fetchCurrentLocation();

            // Refresh layout Leaflet setelah DOM selesai dirender
            setTimeout(() => {
                if (miniMap) {
                    miniMap.invalidateSize();
                }
            }, 300);
        });

        // 2. Client-Side Image Compression with HTML5 Canvas (max 1000px, quality 0.8)
        async function handlePhotoFile(file) {
            if (!file) return;

            // Validate is image
            if (!file.type.startsWith('image/')) {
                alert('File yang dipilih harus berupa gambar (JPG, PNG, WebP).');
                return;
            }

            rawPhotoFile = file;

            // Tampilkan preview segera dengan FileReader agar instan di browser HP
            const quickReader = new FileReader();
            quickReader.onload = (e) => {
                previewImage.src = e.target.result;
                dropzoneArea.classList.add('hidden');
                previewCard.classList.remove('hidden');
            };
            quickReader.readAsDataURL(file);

            try {
                // Kompresi gambar di latar belakang
                const compressionResult = await compressImage(file, 1000, 1000, 0.82);
                compressedPhotoFile = compressionResult.compressedFile;

                // Sync compressed file into input.files via DataTransfer jika didukung
                if (window.DataTransfer) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(compressedPhotoFile);
                        photoInput.files = dt.files;
                    } catch (e) {
                        // ignore DataTransfer restriction on some mobile webviews
                    }
                }

                // Update UI preview
                previewImage.src = compressionResult.previewUrl;
                if (statOriginalSize) statOriginalSize.textContent = formatBytes(compressionResult.originalSize);
                if (statCompressedSize) statCompressedSize.textContent = `${formatBytes(compressionResult.compressedSize)} (${compressionResult.width}x${compressionResult.height}px)`;
                if (statSavingsPercent) {
                    const savings = Math.max(0, Math.round(((compressionResult.originalSize - compressionResult.compressedSize) / compressionResult.originalSize) * 100));
                    statSavingsPercent.textContent = `Hemat ${savings}%`;
                }
            } catch (err) {
                console.warn('Canvas compression dilewati, menggunakan file gambar asli:', err);
                compressedPhotoFile = null;
            }
        }

        const cameraInput = document.getElementById('cameraInput');
        const galleryInput = document.getElementById('galleryInput');

        if (cameraInput) {
            cameraInput.addEventListener('change', (e) => handlePhotoFile(e.target.files[0]));
        }
        if (galleryInput) {
            galleryInput.addEventListener('change', (e) => handlePhotoFile(e.target.files[0]));
        }
        if (photoInput) {
            photoInput.addEventListener('change', (e) => handlePhotoFile(e.target.files[0]));
        }

        function compressImage(file, maxWidth = 1000, maxHeight = 1000, quality = 0.8) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        // Calculate aspect ratio scaling
                        if (width > maxWidth || height > maxHeight) {
                            if (width / maxWidth > height / maxHeight) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            } else {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        // Offscreen HTML5 Canvas
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');

                        // Clean drawing
                        ctx.imageSmoothingEnabled = true;
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, 0, 0, width, height);

                        // Export to Blob
                        canvas.toBlob((blob) => {
                            if (!blob) {
                                reject(new Error('Canvas toBlob failed'));
                                return;
                            }

                            const safeName = (file.name || 'selfie.jpg').replace(/\.[^/.]+$/, "") + ".jpg";
                            const compressedFile = new File([blob], safeName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });

                            resolve({
                                compressedFile,
                                previewUrl: canvas.toDataURL('image/jpeg', quality),
                                width,
                                height,
                                originalSize: file.size,
                                compressedSize: compressedFile.size
                            });
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = reject;
                };
                reader.onerror = reject;
            });
        }

        // 3. Form Submit with AJAX & Safety Check
        visitForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate GPS
            if (!latitudeInput.value || !longitudeInput.value) {
                alert('Titik GPS belum terdeteksi. Silakan klik tombol "Perbarui GPS" atau geser pin pada peta.');
                fetchCurrentLocation();
                return;
            }

            // Validate photo
            const fileToUpload = compressedPhotoFile || rawPhotoFile || (photoInput.files && photoInput.files[0]);
            if (!fileToUpload) {
                alert('Silakan ambil foto bukti kunjungan lapangan terlebih dahulu.');
                return;
            }

            // Prepare FormData
            const formData = new FormData(visitForm);
            // Pastikan file foto selalu terlampir dengan baik
            formData.set('photo', fileToUpload, fileToUpload.name || 'foto_lapangan.jpg');

            // Disable submit button & show loading state
            setSubmittingState(true);

            try {
                const response = await fetch(visitForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    // Show success modal
                    showSuccessModal(data.message);
                } else {
                    let errorMsg = data.message || 'Terjadi kesalahan saat menyimpan data.';
                    if (data.errors) {
                        const details = Object.values(data.errors).flat().join('\n• ');
                        errorMsg += '\n\n• ' + details;
                    }
                    alert(errorMsg);
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert('Gagal mengirim data. Pastikan koneksi internet stabil dan coba tekan "Simpan Kunjungan Lapangan" lagi.');
            } finally {
                setSubmittingState(false);
            }
        });

        function setSubmittingState(isSubmitting) {
            submitBtn.disabled = isSubmitting;
            if (isSubmitting) {
                submitIcon.innerHTML = `<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>`;
                submitIcon.classList.add('animate-spin');
                submitText.textContent = 'Mengunggah Data & Foto...';
            } else {
                submitIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`;
                submitIcon.classList.remove('animate-spin');
                submitText.textContent = 'Simpan Kunjungan Lapangan';
            }
        }

        function showSuccessModal(msg) {
            if (msg) {
                document.getElementById('successModalMessage').textContent = msg;
            }
            successModal.classList.remove('hidden');
            setTimeout(() => {
                successModalBox.classList.remove('scale-95', 'opacity-0');
                successModalBox.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function resetFormAndCloseModal() {
            successModalBox.classList.remove('scale-100', 'opacity-100');
            successModalBox.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                successModal.classList.add('hidden');
                visitForm.reset();
                if (cameraInput) cameraInput.value = '';
                if (galleryInput) galleryInput.value = '';
                if (photoInput) photoInput.value = '';
                rawPhotoFile = null;
                compressedPhotoFile = null;
                clearOfficerInput();
                previewCard.classList.add('hidden');
                dropzoneArea.classList.remove('hidden');
                fetchCurrentLocation();
            }, 200);
        }
    </script>
</body>
</html>
