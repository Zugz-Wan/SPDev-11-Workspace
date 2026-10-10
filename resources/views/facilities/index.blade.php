@extends('layouts.app')

@section('title', 'Daftar Fasilitas')

@section('content')
<div class="space-y-6">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-2xl relative z-10">
            <span class="inline-block px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-semibold mb-3">
                Katalog & Status Fasilitas Real-time
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Ketersediaan Fasilitas Kampus</h1>
            <p class="mt-2 text-slate-300 text-sm sm:text-base leading-relaxed">
                Pantau kondisi ketersediaan ruangan, aula, dan laboratorium. Fasilitas akan secara otomatis beralih status menjadi <span class="text-amber-400 font-semibold">Dalam Perbaikan</span> saat laporan aduan sedang ditangani oleh teknisi.
            </p>
        </div>
    </div>

    <!-- Filter & Search Bar (Tipe, Lokasi, Kapasitas, Status, Search) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <form action="{{ route('facilities.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- 1. Search Keyword -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Fasilitas</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama fasilitas..."
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- 2. Tipe Fasilitas -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tipe Fasilitas</label>
                    <select name="tipe" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ $currentType === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Lokasi / Gedung -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Lokasi / Gedung</label>
                    <select name="lokasi" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ $currentLocation === $loc ? 'selected' : '' }}>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Kapasitas -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kapasitas</label>
                    <select name="kapasitas" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Kapasitas</option>
                        <option value="small" {{ $currentCapacity === 'small' ? 'selected' : '' }}>Kecil (≤ 20 Orang)</option>
                        <option value="medium" {{ $currentCapacity === 'medium' ? 'selected' : '' }}>Sedang (21 - 50 Orang)</option>
                        <option value="large" {{ $currentCapacity === 'large' ? 'selected' : '' }}>Besar (51 - 100 Orang)</option>
                        <option value="xlarge" {{ $currentCapacity === 'xlarge' ? 'selected' : '' }}>Sangat Besar (> 100 Orang)</option>
                    </select>
                </div>

                <!-- 5. Status Ketersediaan -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Ketersediaan</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ $currentStatus === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="perbaikan" {{ $currentStatus === 'perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="tidak_aktif" {{ $currentStatus === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end space-x-2 pt-1 border-t border-slate-100">
                <a href="{{ route('facilities.index') }}" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 text-center transition">
                    Reset Filter
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-100 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Facility Cards Grid -->
    @if($facilities->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-800">Tidak ada fasilitas ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian, tipe, lokasi, atau kapasitas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($facilities as $facility)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden {{ $facility->isUnderRepair() ? 'border-amber-300 ring-1 ring-amber-300/50' : '' }}">
                    <div class="p-6">
                        <!-- Top status badge & Type -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $facility->status_badge_classes }}">
                                @if($facility->isUnderRepair())
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1 animate-pulse"></span>
                                @elseif($facility->isAvailable())
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>
                                @endif
                                {{ $facility->status_label }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-md">
                                {{ $facility->tipe }}
                            </span>
                        </div>

                        <!-- Facility Name & Location -->
                        <a href="{{ route('facilities.show', $facility->id) }}" class="group/title block">
                            <h3 class="text-lg font-bold text-slate-900 leading-snug group-hover/title:text-indigo-600 transition-colors">{{ $facility->nama }}</h3>
                        </a>
                        <p class="text-xs text-slate-500 flex items-center mt-1">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $facility->lokasi }}
                        </p>

                        <!-- Description -->
                        <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                            {{ $facility->deskripsi ?? 'Tidak ada deskripsi rinci untuk fasilitas ini.' }}
                        </p>

                        <!-- Under Repair Warning -->
                        @if($facility->isUnderRepair())
                            <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start space-x-2">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <strong class="block font-semibold">Sedang Dalam Perbaikan</strong>
                                    <span>Laporan kerusakan fasilitas ini sedang ditangani oleh tim teknis.</span>
                                </div>
                            </div>
                        @endif

                        <!-- Capacity & Active Reports Count -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Kapasitas: <strong>{{ $facility->kapasitas }} Orang</strong></span>
                            @if($facility->reports_count > 0)
                                <span class="text-indigo-600 font-semibold">{{ $facility->reports_count }} aduan aktif</span>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div class="p-3.5 bg-slate-50 border-t border-slate-100 grid grid-cols-2 gap-2">
                        <a href="{{ route('facilities.show', $facility->id) }}"
                           class="text-center px-3 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-100 transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Ajukan Reservasi</span>
                        </a>

                        <a href="{{ route('reports.create', ['facility_id' => $facility->id]) }}"
                           class="text-center px-3 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm shadow-rose-100 transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Laporkan Masalah</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $facilities->links() }}
        </div>
    @endif
</div>
@endsection
