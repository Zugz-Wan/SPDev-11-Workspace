@extends('layouts.app')

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
                Pantau kondisi ketersediaan ruangan, aula, dan laboratorium. Fasilitas akan secara otomatis beralih status menjadi <span class="text-amber-400 font-semibold">Dalam Perbaikan</span> saat laporan aduan sedang ditangani oleh teknisi (FR-REP-05).
            </p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('facilities.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama fasilitas, gedung, lokasi..." class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Status Ketersediaan</option>
                    <option value="tersedia" {{ $currentStatus === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="perbaikan" {{ $currentStatus === 'perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    <option value="tidak_aktif" {{ $currentStatus === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                    Filter Fasilitas
                </button>
                <a href="{{ route('facilities.index') }}" class="px-3 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Facility Cards Grid -->
    @if($facilities->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-800">Tidak ada fasilitas ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian lain.</p>
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
                        <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $facility->nama }}</h3>
                        <p class="text-xs text-slate-500 flex items-center mt-1">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $facility->lokasi }}
                        </p>

                        <!-- Description -->
                        <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                            {{ $facility->deskripsi ?? 'Tidak ada deskripsi rinci untuk fasilitas ini.' }}
                        </p>

                        <!-- Under Repair Warning (FR-REP-05) -->
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

                    <!-- Bottom Action Button -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('reports.create', ['facility_id' => $facility->id]) }}" class="w-full text-center px-4 py-2 rounded-xl text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                            + Laporkan Masalah Fasilitas Ini
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
@section('title', 'Daftar Fasilitas')

@section('content')
<h1 class="text-3xl font-bold mb-6">Daftar Fasilitas Kampus</h1>

@if($facilities->count() === 0)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
        Belum ada fasilitas tersedia.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($facilities as $facility)
            <div class="bg-white rounded shadow p-4 hover:shadow-lg transition">
                <h2 class="text-xl font-bold mb-2">{{ $facility->nama }}</h2>
                <p class="text-sm text-gray-600 mb-1">
                    <strong>Tipe:</strong> {{ ucfirst($facility->tipe) }}
                </p>
                <p class="text-sm text-gray-600 mb-1">
                    <strong>Kapasitas:</strong> {{ $facility->kapasitas }} orang
                </p>
                <p class="text-sm text-gray-600 mb-3">
                    <strong>Lokasi:</strong> {{ $facility->lokasi }}
                </p>
                <a href="{{ route('facilities.show', $facility->id) }}"
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Lihat Detail
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $facilities->links() }}
    </div>
@endif
@endsection
