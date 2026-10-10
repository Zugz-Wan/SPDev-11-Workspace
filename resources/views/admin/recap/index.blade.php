@extends('layouts.app')

@section('title', 'Rekap Okupansi & Kerusakan Fasilitas - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Rekapitulasi Lintas Fasilitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laporan tingkat okupansi peminjaman dan frekuensi aduan kerusakan per fasilitas/lokasi.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.recap.export') }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-100 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor Laporan (CSV / Excel)
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Fasilitas Kampus</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalFacilities }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                🏛️
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Reservasi Disetujui</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalReservations }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                📅
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Laporan Kerusakan</p>
                <p class="text-2xl font-bold text-rose-600 mt-1">{{ $totalReports }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                🛠️
            </div>
        </div>
    </div>

    <!-- Table Recap -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Tabel Okupansi & Frekuensi Kerusakan per Fasilitas</h2>
            <span class="text-xs text-slate-400 font-medium">Diperbarui real-time</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Fasilitas</th>
                        <th class="px-4 py-3.5">Lokasi / Tipe</th>
                        <th class="px-4 py-3.5">Status Saat Ini</th>
                        <th class="px-4 py-3.5 text-center">Okupansi (Reservasi)</th>
                        <th class="px-4 py-3.5 text-center">Total Aduan Kerusakan</th>
                        <th class="px-4 py-3.5 text-center">Dalam Penanganan</th>
                        <th class="px-4 py-3.5 text-center">Terselesaikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($facilities as $f)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-slate-900">
                                {{ $f->nama }}
                                <span class="block text-[11px] font-normal text-slate-500">Kapasitas: {{ $f->kapasitas }} orang</span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                <div>{{ $f->lokasi }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    {{ ucfirst($f->tipe) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($f->status === 'tersedia') bg-emerald-100 text-emerald-800
                                    @elseif($f->status === 'perbaikan') bg-amber-100 text-amber-800
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $f->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-semibold text-indigo-700">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-indigo-50 font-mono">
                                    {{ $f->total_reservations }} kali
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-700">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 font-mono">
                                    {{ $f->total_reports }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($f->active_damage_reports > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        {{ $f->active_damage_reports }} aktif
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($f->resolved_reports > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $f->resolved_reports }} tuntas
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data fasilitas untuk direkap.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
