<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Peta Kunjungan Lapangan | Field Visit Tracking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet.js CSS -->
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
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #map {
            width: 100vw;
            height: 100vh;
            z-index: 1;
        }

        /* Custom Leaflet Marker Styling */
        .custom-pin-marker {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pin-wrapper {
            position: relative;
            filter: drop-shadow(0 6px 10px rgba(0,0,0,0.3));
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .pin-wrapper:hover {
            transform: scale(1.15) translateY(-4px);
        }

        /* Leaflet Popup Customization */
        .leaflet-popup-content-wrapper {
            padding: 0 !important;
            border-radius: 1rem !important;
            overflow: hidden !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            line-height: normal !important;
            width: 290px !important;
        }
        .leaflet-popup-tip-container {
            margin-top: -1px;
        }
        .leaflet-container a.leaflet-popup-close-button {
            top: 8px !important;
            right: 8px !important;
            color: #ffffff !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border-radius: 9999px !important;
            width: 26px !important;
            height: 26px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            backdrop-filter: blur(4px) !important;
            transition: background 0.2s;
            z-index: 20;
        }
        .leaflet-container a.leaflet-popup-close-button:hover {
            background: rgba(15, 23, 42, 0.9) !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

    <!-- Top Floating Header Bar -->
    <header class="fixed top-4 left-4 right-4 z-20 pointer-events-none">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-3">
            
            <!-- App Brand & Stats Pill -->
            <div class="pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-lg border border-slate-200/80 px-4 py-2.5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-sm font-bold text-slate-900 leading-none">Field Visit Mapping</h1>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Live
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Total: <span id="visitCountBadge" class="font-bold text-brand-600">{{ $visits->count() }}</span> Titik Kunjungan
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pointer-events-auto flex items-center gap-2.5">
                <!-- Toggle Sidebar List Button -->
                <button type="button"
                        onclick="toggleSidebar()"
                        class="bg-white/95 backdrop-blur-md hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-2xl border border-slate-200/80 shadow-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer active:scale-95">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Daftar Kunjungan</span>
                </button>

                <!-- Create Visit Button -->
                <a href="{{ route('visits.create') }}"
                   class="bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 active:scale-95 text-white px-4 py-2.5 rounded-2xl shadow-lg shadow-brand-500/25 text-xs font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Kunjungan</span>
                </a>
            </div>

        </div>
    </header>

    <!-- Floating Map Control Buttons (Bottom Right) -->
    <div class="fixed bottom-6 right-6 z-20 flex flex-col gap-2.5">
        <!-- Fokus Seluruh Indonesia -->
        <button type="button"
                onclick="fitIndonesia()"
                title="Fokus Seluruh Indonesia (NKRI)"
                class="w-11 h-11 bg-white hover:bg-slate-50 text-slate-700 rounded-2xl shadow-lg border border-slate-200 flex items-center justify-center transition active:scale-95 cursor-pointer group">
            <span class="text-xs font-black text-brand-600 group-hover:scale-110 transition tracking-tighter">IDN</span>
        </button>

        <!-- Re-center on all markers -->
        <button type="button"
                onclick="fitAllMarkers()"
                title="Fokus Semua Kunjungan"
                class="w-11 h-11 bg-white hover:bg-slate-50 text-slate-700 rounded-2xl shadow-lg border border-slate-200 flex items-center justify-center transition active:scale-95 cursor-pointer group">
            <svg class="w-5 h-5 text-slate-600 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
            </svg>
        </button>

        <!-- Current User Location -->
        <button type="button"
                id="myLocationBtn"
                onclick="goToUserLocation()"
                title="Lokasi Saya Saat Ini"
                class="w-11 h-11 bg-white hover:bg-slate-50 text-slate-700 rounded-2xl shadow-lg border border-slate-200 flex items-center justify-center transition active:scale-95 cursor-pointer group">
            <svg id="locationIcon" class="w-5 h-5 text-slate-600 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
            </svg>
        </button>
    </div>

    <!-- Empty State Banner (Shown when no visits exist) -->
    @if($visits->isEmpty())
        <div id="emptyNotice" class="fixed inset-0 z-10 pointer-events-none flex items-center justify-center p-4">
            <div class="pointer-events-auto bg-white/95 backdrop-blur-md rounded-3xl p-6 md:p-8 max-w-sm w-full text-center shadow-2xl border border-slate-200">
                <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-brand-100 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">Belum Ada Kunjungan</h2>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Database kunjungan masih kosong. Klik tombol di bawah untuk mencatat kunjungan lapangan perdana dengan swafoto & titik GPS.
                </p>
                <div class="mt-6">
                    <a href="{{ route('visits.create') }}" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-brand-500/25">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Input Kunjungan Baru
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Slide-over Drawer for Visits List (Spacious & Modern) -->
    <aside id="visitsSidebar" class="fixed top-0 right-0 bottom-0 z-30 w-full sm:w-[480px] md:w-[520px] lg:w-[560px] max-w-[95vw] bg-white shadow-2xl border-l border-slate-200 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Sidebar Header -->
        <div class="p-4 sm:p-5 border-b border-slate-200/90 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center border border-brand-100 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Daftar Kunjungan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Menampilkan <span id="headerMatchCount" class="font-bold text-brand-600">{{ $visits->count() }}</span> dari {{ $visits->count() }} kunjungan
                    </p>
                </div>
            </div>

            <button type="button"
                    onclick="toggleSidebar()"
                    title="Tutup Panel"
                    class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Filter & Sorting Panel -->
        <div class="p-4 sm:p-5 border-b border-slate-200/80 bg-slate-50/70 space-y-3.5">
            <!-- Filter Petugas Dropdown -->
            <div>
                <label for="officerSelectFilter" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Filter Petugas
                    </span>
                </label>
                <select id="officerSelectFilter"
                        onchange="applyAllFilters()"
                        class="w-full bg-white border border-slate-300 hover:border-slate-400 text-slate-800 text-xs sm:text-sm font-medium rounded-xl px-3.5 py-2.5 shadow-2xs focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer transition">
                    <option value="">Semua Petugas ({{ $visits->count() }} Kunjungan)</option>
                    @foreach($officers as $officer)
                        <option value="{{ strtolower($officer) }}">{{ $officer }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort and Time Filters in Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="sortSelectFilter" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                        Urutkan
                    </label>
                    <select id="sortSelectFilter"
                            onchange="applyAllFilters()"
                            class="w-full bg-white border border-slate-300 hover:border-slate-400 text-slate-800 text-xs sm:text-sm font-medium rounded-xl px-3 py-2.5 shadow-2xs focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer transition">
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="name_asc">Nama (A &rarr; Z)</option>
                        <option value="name_desc">Nama (Z &rarr; A)</option>
                    </select>
                </div>
                <div>
                    <label for="timeSelectFilter" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Periode Waktu
                    </label>
                    <select id="timeSelectFilter"
                            onchange="applyAllFilters()"
                            class="w-full bg-white border border-slate-300 hover:border-slate-400 text-slate-800 text-xs sm:text-sm font-medium rounded-xl px-3 py-2.5 shadow-2xs focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer transition">
                        <option value="all">Semua Waktu</option>
                        <option value="today">Hari Ini</option>
                        <option value="7days">7 Hari Terakhir</option>
                    </select>
                </div>
            </div>

            <!-- Real-time Search Box -->
            <div class="relative">
                <input type="text"
                       id="sidebarSearchInput"
                       oninput="applyAllFilters()"
                       placeholder="Cari nama petugas atau keterangan kegiatan..."
                       class="w-full bg-white pl-10 pr-4 py-2.5 rounded-xl text-xs sm:text-sm border border-slate-300 hover:border-slate-400 text-slate-800 placeholder:text-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Active Filter Status & Reset Action -->
            <div id="filterStatusBar" class="hidden flex items-center justify-between text-xs text-slate-700 bg-brand-50/90 border border-brand-200/80 px-3.5 py-2 rounded-xl">
                <span>Ditemukan: <strong id="filterMatchCount" class="text-brand-700 font-extrabold">0</strong> kunjungan</span>
                <button type="button" onclick="resetAllFilters()" class="text-brand-600 font-bold hover:text-brand-800 underline cursor-pointer text-xs flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- List Items Container (Spacious Cards, No Cramping) -->
        <div id="visitsListContainer" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3.5 bg-slate-100/60">
            @forelse($visits as $visit)
                <div id="visit-card-{{ $visit->id }}"
                     onclick="focusVisit({{ $visit->id }}, {{ $visit->latitude }}, {{ $visit->longitude }})"
                     class="visit-list-item cursor-pointer p-4 bg-white hover:bg-brand-50/25 rounded-2xl border border-slate-200/90 hover:border-brand-400 transition-all duration-200 group shadow-2xs hover:shadow-md"
                     data-id="{{ $visit->id }}"
                     data-name="{{ strtolower($visit->visitor_name) }}"
                     data-time="{{ $visit->created_at ? $visit->created_at->timestamp : 0 }}"
                     data-notes="{{ strtolower($visit->notes ?? '') }}">
                    
                    <div class="flex items-start gap-3.5">
                        <!-- Visitor Selfie Thumbnail -->
                        <div class="relative shrink-0">
                            <img src="{{ $visit->photo_url }}"
                                 alt="{{ $visit->visitor_name }}"
                                 onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80'"
                                 class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl object-cover border border-slate-200 shadow-2xs group-hover:scale-102 transition duration-200">
                            <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-brand-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold shadow-xs">
                                ✓
                            </span>
                        </div>

                        <!-- Info Content -->
                        <div class="min-w-0 flex-1">
                            <!-- Visitor Name & Time Badge -->
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 group-hover:text-brand-600 transition leading-snug break-words">
                                    {{ $visit->visitor_name }}
                                </h3>
                                <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200/60 shrink-0 whitespace-nowrap">
                                    {{ $visit->created_at ? $visit->created_at->diffForHumans() : '-' }}
                                </span>
                            </div>

                            <!-- Keterangan Kegiatan (Jika Ada) - Tampil Utuh Tanpa Terpotong -->
                            @if(!empty($visit->notes))
                                <div class="mt-2.5 bg-slate-50 border border-slate-200/80 rounded-xl p-3 text-slate-700">
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-brand-700 mb-1">
                                        <svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Keterangan Kegiatan</span>
                                    </div>
                                    <p class="leading-relaxed text-xs text-slate-700 font-normal break-words whitespace-pre-line">
                                        {{ $visit->notes }}
                                    </p>
                                </div>
                            @else
                                <p class="text-[11px] text-slate-400 italic mt-1.5">
                                    - Tidak ada keterangan kegiatan -
                                </p>
                            @endif

                            <!-- Meta Details (Date & GPS Coordinates) -->
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                                <span class="flex items-center gap-1.5 font-medium text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $visit->created_at ? $visit->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[10px] text-slate-600 bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded-md">
                                        {{ number_format($visit->latitude, 5) }}, {{ number_format($visit->longitude, 5) }}
                                    </span>
                                    <span class="text-[11px] font-bold text-brand-600 group-hover:translate-x-0.5 transition hidden sm:inline-flex items-center gap-0.5">
                                        Peta &rarr;
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-400 text-xs">
                    Belum ada riwayat kunjungan.
                </div>
            @endforelse

            <div id="emptyFilterNotice" class="hidden text-center py-10 px-4 bg-white rounded-2xl border border-slate-200 text-slate-500 text-xs">
                Tidak ada data kunjungan yang cocok dengan filter yang dipilih.
                <button type="button" onclick="resetAllFilters()" class="block mx-auto mt-2 text-brand-600 font-bold underline">
                    Reset Filter
                </button>
            </div>
        </div>
    </aside>

    <!-- Fullscreen Map Container -->
    <div id="map"></div>

    <!-- Image Lightbox Modal -->
    <div id="imageLightbox"
         onclick="closeLightbox()"
         class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md hidden flex items-center justify-center p-4 cursor-pointer">
        <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" alt="Zoom Foto Lapangan" class="w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border border-white/10">
            <button type="button"
                    onclick="closeLightbox()"
                    class="absolute -top-10 right-0 text-white/80 hover:text-white flex items-center gap-1 text-xs font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Tutup
            </button>
        </div>
    </div>

    <!-- Leaflet.js -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Visits data passed from controller
        const visitsData = @json($visits);

        // Store markers indexed by ID
        const markersById = {};
        const allMarkersGroup = L.featureGroup();

        // Batas Wilayah Negara Kesatuan Republik Indonesia (NKRI)
        // Sabang / Pulau Rote (Barat-Daya: [-11.5, 94.5]) s/d Miangas / Merauke (Timur-Laut: [6.5, 141.5])
        const indonesiaBounds = L.latLngBounds(
            L.latLng(-11.5, 94.5), // Barat Daya
            L.latLng(6.5, 141.5)   // Timur Laut
        );

        // Titik Tengah Kepulauan Indonesia
        const indonesiaCenter = [-2.5489, 118.0149];
        const defaultZoom = 5;

        // Inisialisasi Fullscreen Leaflet Map Terkunci di Indonesia
        const map = L.map('map', {
            zoomControl: false,
            maxBounds: indonesiaBounds,
            maxBoundsViscosity: 1.0, // Peta membal dan terkunci hanya di wilayah Indonesia
            minZoom: 5,              // Mencegah zoom-out ke luar wilayah negara Indonesia
            maxZoom: 19
        }).setView(indonesiaCenter, defaultZoom);

        // Position zoom control at bottom-left
        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // OpenStreetMap Tile Layer dengan batasan Indonesia
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            bounds: indonesiaBounds,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Helper: Format Date String
        function formatVisitDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) + ' WIB';
        }

        // Custom Pin Icon Generator
        function createCustomPin(visit) {
            return L.divIcon({
                className: 'custom-pin-marker',
                iconSize: [44, 52],
                iconAnchor: [22, 50],
                popupAnchor: [0, -48],
                html: `
                    <div class="pin-wrapper">
                        <svg width="44" height="52" viewBox="0 0 44 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 0C9.84974 0 0 9.84974 0 22C0 35.5 19.5 50.5 21.1 51.7C21.6 52.1 22.4 52.1 22.9 51.7C24.5 50.5 44 35.5 44 22C44 9.84974 34.1503 0 22 0Z" fill="#4F46E5"/>
                            <circle cx="22" cy="20" r="16" fill="white"/>
                        </svg>
                        <div style="position: absolute; top: 6px; left: 8px; width: 28px; height: 28px; border-radius: 9999px; overflow: hidden; border: 2px solid #4F46E5;">
                            <img src="${visit.photo_url}"
                                 alt="${visit.visitor_name}"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80'" />
                        </div>
                    </div>
                `
            });
        }

        // Helper: Escape HTML string to prevent XSS in popups
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Generate Leaflet Popup Content HTML
        function generatePopupContent(visit) {
            const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${visit.latitude},${visit.longitude}`;
            const formattedDate = formatVisitDate(visit.created_at);

            return `
                <div class="text-slate-800">
                    <!-- Photo Header (Fixed 180px height so portrait & landscape look identical) -->
                    <div class="relative bg-slate-900 cursor-pointer overflow-hidden group w-full" style="height: 180px; max-height: 180px;" onclick="openLightbox('${visit.photo_url}')">
                        <img src="${visit.photo_url}"
                             alt="${escapeHtml(visit.visitor_name)}"
                             style="width: 100%; height: 180px; object-fit: cover;"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                             onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80'"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                        <div class="absolute bottom-2 left-3 right-3 flex items-center justify-between text-white text-[11px] pointer-events-none">
                            <span class="font-bold flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Swafoto Petugas
                            </span>
                            <span class="bg-black/60 backdrop-blur-xs px-2 py-0.5 rounded-md text-[10px] font-medium">Klik zoom</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-3.5 space-y-2.5">
                        <!-- Visitor Name -->
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Nama Petugas</span>
                            <h4 class="text-sm font-extrabold text-slate-900 leading-tight mt-0.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                                ${escapeHtml(visit.visitor_name)}
                            </h4>
                        </div>

                        <!-- Keterangan Kegiatan (Notes) - Consistent Layout -->
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-slate-700">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1 mb-1">
                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Keterangan Kegiatan
                            </span>
                            <p class="text-xs ${visit.notes ? 'text-slate-700 leading-relaxed max-h-20 overflow-y-auto whitespace-pre-line' : 'text-slate-400 italic'}">
                                ${visit.notes ? escapeHtml(visit.notes) : 'Tidak ada keterangan kegiatan'}
                            </p>
                        </div>

                        <!-- Date & Coordinates -->
                        <div class="space-y-1 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium">${formattedDate}</span>
                            </div>
                            <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span>${parseFloat(visit.latitude).toFixed(6)}, ${parseFloat(visit.longitude).toFixed(6)}</span>
                            </div>
                        </div>

                        <!-- Action Button: Google Maps Directions -->
                        <a href="${directionsUrl}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold flex items-center justify-center gap-1.5 transition shadow-xs">
                            <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>
                </div>
            `;
        }

        // Loop visits data and add L.marker
        visitsData.forEach(visit => {
            const lat = parseFloat(visit.latitude);
            const lng = parseFloat(visit.longitude);

            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng], {
                    icon: createCustomPin(visit),
                    title: visit.visitor_name
                });

                marker.bindPopup(generatePopupContent(visit), {
                    maxWidth: 320,
                    minWidth: 280,
                    autoPan: false
                });

                marker.on('click', function () {
                    focusVisit(visit.id, lat, lng);
                });

                markersById[visit.id] = marker;
                allMarkersGroup.addLayer(marker);
            }
        });

        // Add all markers to map
        allMarkersGroup.addTo(map);

        // Auto fit bounds if markers exist within Indonesia
        if (visitsData.length > 0) {
            map.fitBounds(allMarkersGroup.getBounds(), {
                padding: [80, 80],
                maxZoom: 15
            });
        } else {
            map.fitBounds(indonesiaBounds, {
                padding: [20, 20]
            });
        }

        // Control: Fokus Seluruh Indonesia
        function fitIndonesia() {
            map.fitBounds(indonesiaBounds, {
                padding: [20, 20]
            });
        }

        // Control: Fit All Markers
        function fitAllMarkers() {
            if (allMarkersGroup.getLayers().length > 0) {
                map.fitBounds(allMarkersGroup.getBounds(), {
                    padding: [80, 80],
                    maxZoom: 15
                });
            } else {
                map.fitBounds(indonesiaBounds, {
                    padding: [20, 20]
                });
            }
        }

        // Control: User Geolocation
        let userLocationMarker = null;
        function goToUserLocation() {
            const locIcon = document.getElementById('locationIcon');
            locIcon.classList.add('animate-spin');

            if (!navigator.geolocation) {
                alert('Browser tidak mendukung geolokasi GPS.');
                locIcon.classList.remove('animate-spin');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    
                    locIcon.classList.remove('animate-spin');

                    if (userLocationMarker) {
                        map.removeLayer(userLocationMarker);
                    }

                    // Pulsing blue dot for user current location
                    const userDot = L.divIcon({
                        className: 'user-loc-dot',
                        html: `
                            <div style="position:relative; width: 20px; height: 20px;">
                                <div style="position:absolute; inset: 0; border-radius: 9999px; background-color: #3b82f6; opacity: 0.5; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
                                <div style="position:absolute; inset: 2px; border-radius: 9999px; background-color: #2563eb; border: 2px solid #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>
                            </div>
                        `,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });

                    userLocationMarker = L.marker([lat, lng], { icon: userDot })
                        .addTo(map)
                        .bindPopup('<b>Lokasi Anda Saat Ini</b><br>Akurasi: ±' + Math.round(pos.coords.accuracy) + 'm')
                        .openPopup();

                    map.flyTo([lat, lng], 16, { duration: 1.2 });
                },
                (err) => {
                    locIcon.classList.remove('animate-spin');
                    alert('Tidak dapat mendeteksi lokasi: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        // Helper: Hitung titik tengah LatLng agar Popup berada presisi di tengah area peta yang terlihat
        function getCenteredLatLng(lat, lng, targetZoom) {
            const zoom = targetZoom || map.getZoom() || 17;

            // Hitung lebar sidebar jika terbuka di layar desktop
            const isSidebarOpen = visitsSidebar && !visitsSidebar.classList.contains('translate-x-full');
            const sidebarWidth = (isSidebarOpen && window.innerWidth >= 768) ? visitsSidebar.offsetWidth : 0;

            // Posisi X target: tengah area peta yang terlihat (antara sisi kiri layar dan sisi kiri drawer sidebar)
            const targetScreenX = (window.innerWidth - sidebarWidth) / 2;

            // Posisi Y target: popup memiliki tinggi ~420px dan berada di atas pin marker (+50px).
            // Agar seluruh kartu popup berada pas di tengah layar secara vertikal, titik koordinat pin harus berada di bawah tengah layar (+210px).
            let targetScreenY = (window.innerHeight / 2) + 210;

            // Pastikan pin marker tidak keluar batas bawah layar
            if (targetScreenY > window.innerHeight - 60) {
                targetScreenY = window.innerHeight - 60;
            }
            // Pastikan bagian atas popup tidak menabrak header mengambang atas (~85px)
            if (targetScreenY - 440 < 85) {
                targetScreenY = Math.min(window.innerHeight - 60, 85 + 440);
            }

            const screenCenterX = window.innerWidth / 2;
            const screenCenterY = window.innerHeight / 2;

            const offsetX = targetScreenX - screenCenterX;
            const offsetY = targetScreenY - screenCenterY;

            const targetPoint = map.project([lat, lng], zoom);
            const centerPoint = targetPoint.subtract([offsetX, offsetY]);

            return map.unproject(centerPoint, zoom);
        }

        // Focus specific visit from sidebar or marker click (Centered in visible viewport)
        function focusVisit(id, lat, lng) {
            const marker = markersById[id];
            if (!marker) return;

            // Di mobile, tutup sidebar saat memilih agar peta langsung terlihat
            if (window.innerWidth < 768) {
                toggleSidebar(false);
            }

            const targetZoom = Math.max(map.getZoom() || 0, 17);
            const centeredLatLng = getCenteredLatLng(lat, lng, targetZoom);

            // Highlight kartu kunjungan aktif di sidebar
            document.querySelectorAll('.visit-list-item').forEach(el => {
                el.classList.remove('ring-2', 'ring-brand-500', 'bg-brand-50/40');
            });
            const activeCard = document.getElementById(`visit-card-${id}`);
            if (activeCard) {
                activeCard.classList.add('ring-2', 'ring-brand-500', 'bg-brand-50/40');
                activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            marker.openPopup();
            map.flyTo(centeredLatLng, targetZoom, {
                duration: 0.8
            });
        }

        // Sidebar Toggle
        const visitsSidebar = document.getElementById('visitsSidebar');
        function toggleSidebar(forceState) {
            const isClosed = visitsSidebar.classList.contains('translate-x-full');
            const shouldOpen = forceState !== undefined ? forceState : isClosed;

            if (shouldOpen) {
                visitsSidebar.classList.remove('translate-x-full');
            } else {
                visitsSidebar.classList.add('translate-x-full');
            }
        }

        // Global Filter & Sorting Engine
        function applyAllFilters() {
            const officerFilter = (document.getElementById('officerSelectFilter')?.value || '').toLowerCase().trim();
            const sortFilter = document.getElementById('sortSelectFilter')?.value || 'latest';
            const timeFilter = document.getElementById('timeSelectFilter')?.value || 'all';
            const searchQuery = (document.getElementById('sidebarSearchInput')?.value || '').toLowerCase().trim();
            const itemsContainer = document.getElementById('visitsListContainer');
            const emptyNotice = document.getElementById('emptyFilterNotice');
            const items = Array.from(document.querySelectorAll('.visit-list-item'));

            const now = Math.floor(Date.now() / 1000);
            const startOfToday = new Date();
            startOfToday.setHours(0, 0, 0, 0);
            const todayTimestamp = Math.floor(startOfToday.getTime() / 1000);
            const sevenDaysAgoTimestamp = now - (7 * 86400);

            let visibleCount = 0;

            // 1. Filter items and synchronize map markers
            items.forEach(item => {
                const id = item.getAttribute('data-id');
                const name = (item.getAttribute('data-name') || '').toLowerCase();
                const notes = (item.getAttribute('data-notes') || '').toLowerCase();
                const time = parseInt(item.getAttribute('data-time'), 10) || 0;
                const marker = markersById[id];

                // Check Officer Filter
                let matchesOfficer = true;
                if (officerFilter && officerFilter !== '') {
                    matchesOfficer = (name === officerFilter);
                }

                // Check Time Period Filter
                let matchesTime = true;
                if (timeFilter === 'today') {
                    matchesTime = (time >= todayTimestamp);
                } else if (timeFilter === '7days') {
                    matchesTime = (time >= sevenDaysAgoTimestamp);
                }

                // Check Search Query (matches name or notes)
                let matchesSearch = true;
                if (searchQuery) {
                    matchesSearch = name.includes(searchQuery) || notes.includes(searchQuery);
                }

                const isMatch = matchesOfficer && matchesTime && matchesSearch;

                if (isMatch) {
                    item.classList.remove('hidden');
                    visibleCount++;

                    // Show Marker on Map
                    if (marker && !allMarkersGroup.hasLayer(marker)) {
                        allMarkersGroup.addLayer(marker);
                    }
                } else {
                    item.classList.add('hidden');

                    // Hide Marker from Map
                    if (marker && allMarkersGroup.hasLayer(marker)) {
                        allMarkersGroup.removeLayer(marker);
                    }
                }
            });

            // 2. Sort items in DOM according to sortFilter
            items.sort((a, b) => {
                const timeA = parseInt(a.getAttribute('data-time'), 10) || 0;
                const timeB = parseInt(b.getAttribute('data-time'), 10) || 0;
                const nameA = (a.getAttribute('data-name') || '').toLowerCase();
                const nameB = (b.getAttribute('data-name') || '').toLowerCase();

                switch (sortFilter) {
                    case 'oldest':
                        return timeA - timeB;
                    case 'name_asc':
                        return nameA.localeCompare(nameB);
                    case 'name_desc':
                        return nameB.localeCompare(nameA);
                    case 'latest':
                    default:
                        return timeB - timeA;
                }
            });

            // Re-order DOM nodes
            items.forEach(item => {
                if (emptyNotice) {
                    itemsContainer.insertBefore(item, emptyNotice);
                } else {
                    itemsContainer.appendChild(item);
                }
            });

            // 3. Update counter badges
            const totalCount = items.length;
            const headerMatchEl = document.getElementById('headerMatchCount');
            if (headerMatchEl) headerMatchEl.textContent = visibleCount;

            const filterMatchEl = document.getElementById('filterMatchCount');
            if (filterMatchEl) filterMatchEl.textContent = visibleCount;

            const visitCountBadge = document.getElementById('visitCountBadge');
            if (visitCountBadge) {
                visitCountBadge.textContent = (visibleCount === totalCount) ? totalCount : `${visibleCount}/${totalCount}`;
            }

            // 4. Show/hide Filter Status Bar and Empty Notice
            const isFilterActive = (officerFilter !== '') || (sortFilter !== 'latest') || (timeFilter !== 'all') || (searchQuery !== '');
            const filterStatusBar = document.getElementById('filterStatusBar');
            if (filterStatusBar) {
                if (isFilterActive) {
                    filterStatusBar.classList.remove('hidden');
                } else {
                    filterStatusBar.classList.add('hidden');
                }
            }

            if (emptyNotice) {
                if (visibleCount === 0 && totalCount > 0) {
                    emptyNotice.classList.remove('hidden');
                } else {
                    emptyNotice.classList.add('hidden');
                }
            }
        }

        // Reset all filters back to default
        function resetAllFilters() {
            const officerSelect = document.getElementById('officerSelectFilter');
            if (officerSelect) officerSelect.value = '';

            const sortSelect = document.getElementById('sortSelectFilter');
            if (sortSelect) sortSelect.value = 'latest';

            const timeSelect = document.getElementById('timeSelectFilter');
            if (timeSelect) timeSelect.value = 'all';

            const sidebarSearch = document.getElementById('sidebarSearchInput');
            if (sidebarSearch) sidebarSearch.value = '';

            applyAllFilters();
        }

        // Lightbox Functions
        function openLightbox(url) {
            const box = document.getElementById('imageLightbox');
            const img = document.getElementById('lightboxImg');
            img.src = url;
            box.classList.remove('hidden');
        }

        function closeLightbox() {
            const box = document.getElementById('imageLightbox');
            box.classList.add('hidden');
        }
    </script>
</body>
</html>
