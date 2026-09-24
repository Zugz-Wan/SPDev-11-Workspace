@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-purple-50 text-purple-700 border border-purple-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                <span>FR-REP-03 & FR-REP-04 • Dashboard Petugas Teknis</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Antrian Laporan Kerusakan Fasilitas</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola antrian laporan masuk, tindak lanjut perbaikan, dan pembaruan otomatis ketersediaan fasilitas.</p>
        </div>
    </div>

    <!-- Status Metric Cards (FR-REP-03) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <a href="{{ route('officer.reports.index') }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === '' ? 'bg-purple-50 border-purple-200 ring-2 ring-purple-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold text-slate-500">Total Semua Laporan</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['total'] }}</div>
        </a>
        <a href="{{ route('officer.reports.index', ['status' => 'baru']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'baru' ? 'bg-blue-50 border-blue-200 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-blue-700">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Antrian Baru</span>
            </div>
            <div class="text-2xl font-bold text-blue-900 mt-1">{{ $counts['baru'] }}</div>
        </a>
        <a href="{{ route('officer.reports.index', ['status' => 'diproses']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'diproses' ? 'bg-amber-50 border-amber-200 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-amber-700">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Sedang Ditangani</span>
            </div>
            <div class="text-2xl font-bold text-amber-900 mt-1">{{ $counts['diproses'] }}</div>
        </a>
        <a href="{{ route('officer.reports.index', ['status' => 'selesai']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'selesai' ? 'bg-emerald-50 border-emerald-200 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Selesai Diperbaiki</span>
            </div>
            <div class="text-2xl font-bold text-emerald-900 mt-1">{{ $counts['selesai'] }}</div>
        </a>
        <a href="{{ route('officer.reports.index', ['status' => 'ditolak']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'ditolak' ? 'bg-rose-50 border-rose-200 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }} col-span-2 sm:col-span-1">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-rose-700">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Ditolak</span>
            </div>
            <div class="text-2xl font-bold text-rose-900 mt-1">{{ $counts['ditolak'] }}</div>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('officer.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @if($currentStatus !== '')
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            <!-- Search -->
            <div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pelapor, fasilitas, deskripsi..." class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Fasilitas Filter -->
            <div>
                <select name="facility_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}" {{ $currentFacility == $fac->id ? 'selected' : '' }}>
                            {{ $fac->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kategori Filter -->
            <div>
                <select name="kategori" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $currentCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 transition-colors">
                    Terapkan Filter
                </button>
                <a href="{{ route('officer.reports.index') }}" class="px-3 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Queue (FR-REP-03) -->
    @if($reports->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Tidak Ada Antrian Laporan</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto mt-1">
                Semua antrian dalam kondisi bersih atau tidak ada data yang cocok dengan kriteria filter.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($reports as $report)
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between space-y-4">
                    <!-- Card Top -->
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-400">#REP-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="text-base font-bold text-slate-900 mt-0.5">
                                    {{ $report->facility->nama }}
                                </h3>
                                <p class="text-xs text-slate-500">{{ $report->facility->lokasi }}</p>
                            </div>
                            <div class="flex flex-col items-end space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $report->status_badge_classes }}">
                                    {{ $report->status_label }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    Fasilitas: <strong class="{{ $report->facility->status === 'perbaikan' ? 'text-amber-600' : 'text-emerald-600' }}">{{ $report->facility->status_label }}</strong>
                                </span>
                            </div>
                        </div>

                        <!-- Reporter & Category Tag -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <div class="flex items-center space-x-1.5 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>{{ $report->user->name }}</span>
                            </div>
                            <span class="text-slate-300">•</span>
                            <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-semibold text-slate-700 text-[11px]">
                                {{ $report->kategori }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-400 text-[11px]">{{ $report->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Description & Photo -->
                        <div class="flex gap-3">
                            <p class="text-xs text-slate-700 leading-relaxed flex-1 whitespace-pre-line">
                                {{ $report->deskripsi }}
                            </p>
                            @if($report->foto)
                                <a href="{{ asset('storage/' . $report->foto) }}" target="_blank" class="w-20 h-20 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0 group relative bg-slate-100">
                                    <img src="{{ asset('storage/' . $report->foto) }}" alt="Bukti Foto" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                </a>
                            @endif
                        </div>

                        <!-- Existing Officer Note -->
                        @if($report->catatan)
                            <div class="text-xs p-3 rounded-xl bg-purple-50/70 border border-purple-100 text-purple-900">
                                <span class="font-bold block mb-0.5">Catatan Petugas:</span>
                                <span>{{ $report->catatan }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Bottom: Status Update Form (FR-REP-04 & FR-REP-05) -->
                    <div class="pt-3 border-t border-slate-100">
                        <form action="{{ route('officer.reports.update-status', $report) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Ubah Status Laporan:</label>
                                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-medium">
                                        <option value="baru" {{ $report->status === 'baru' ? 'selected' : '' }}>Baru (Antrian Masuk)</option>
                                        <option value="diproses" {{ $report->status === 'diproses' ? 'selected' : '' }}>Diproses (Sedang Ditangani)</option>
                                        <option value="selesai" {{ $report->status === 'selesai' ? 'selected' : '' }}>Selesai (Diperbaiki)</option>
                                        <option value="ditolak" {{ $report->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Catatan Petugas:</label>
                                    <input type="text" name="catatan" value="{{ old('catatan', $report->catatan) }}" placeholder="Catatan perbaikan/alasan..." class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                </div>
                            </div>

                            <!-- Automatic sync info notice (FR-REP-05) -->
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span class="inline-flex items-center text-[10px] text-slate-400">
                                    <svg class="w-3 h-3 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                    FR-REP-05: Otomatis sinkron status fasilitas
                                </span>
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 shadow-sm transition-colors">
                                    Perbarui Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
