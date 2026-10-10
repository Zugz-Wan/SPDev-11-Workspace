@extends('layouts.app')

@section('title', 'Rekapitulasi Okupansi & Kerusakan Fasilitas')

@section('content')
<div class="space-y-8">
    {{-- Header & Action Buttons --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Admin Panel • Rekapitulasi & Pelaporan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Rekap Okupansi & Kerusakan</h1>
            <p class="text-slate-500 text-sm mt-1">
                Laporan komprehensif tingkat okupansi reservasi disetujui, akumulasi jam pemakaian, dan frekuensi kerusakan fasilitas.
            </p>
        </div>

        {{-- Action Export Buttons (Hanya 2 Pilihan Format: CSV dan PDF) --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Dropdown / Group Ekspor CSV --}}
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button type="button"
                        onclick="document.getElementById('csv-menu').classList.toggle('hidden')"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Ekspor CSV</span>
                    <svg class="w-3.5 h-3.5 ml-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="csv-menu"
                     class="hidden absolute right-0 z-20 mt-2 w-56 origin-top-right rounded-2xl bg-white shadow-xl ring-1 ring-black/5 border border-slate-100 p-1.5 space-y-1 focus:outline-none">
                    <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'all'])) }}"
                       class="flex items-center px-3 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                        Semua Rekap (Lengkap)
                    </a>
                    <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'occupancy'])) }}"
                       class="flex items-center px-3 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                        Hanya Okupansi Fasilitas
                    </a>
                    <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'damage_facility'])) }}"
                       class="flex items-center px-3 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                        <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span>
                        Hanya Kerusakan per Fasilitas
                    </a>
                    <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'damage_location'])) }}"
                       class="flex items-center px-3 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                        Hanya Kerusakan per Lokasi
                    </a>
                </div>
            </div>

            {{-- Tombol Cetak / PDF --}}
            <a href="{{ route('admin.rekap.print', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-100 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak / Ekspor PDF</span>
            </a>
        </div>
    </div>

    {{-- Filter Panel Rentang Tanggal --}}
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Filter Rentang Tanggal Laporan</h2>
                    <p class="text-xs text-slate-500">Filter data okupansi reservasi dan tanggal pelaporan kerusakan.</p>
                </div>
            </div>

            {{-- Indikator Status Filter Aktif --}}
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                <span class="w-2 h-2 rounded-full {{ $startDate || $endDate ? 'bg-indigo-600' : 'bg-slate-400' }} mr-2"></span>
                <span>Periode: <strong class="text-slate-900">{{ $periodeLabel }}</strong></span>
            </div>
        </div>

        <form id="filter-form" action="{{ route('admin.rekap.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Selesai</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-100">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.rekap.index') }}"
                       class="px-4 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 transition-colors">
                        Reset
                    </a>
                </div>
            </div>

            {{-- Quick Presets --}}
            <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs text-slate-500">
                <span class="font-semibold text-slate-600 mr-1">Preset Cepat:</span>
                @php
                    $today = now()->format('Y-m-d');
                    $thisMonthStart = now()->startOfMonth()->format('Y-m-d');
                    $thisMonthEnd = now()->endOfMonth()->format('Y-m-d');
                    $last30Days = now()->subDays(30)->format('Y-m-d');
                    $thisYearStart = now()->startOfYear()->format('Y-m-d');
                    $thisYearEnd = now()->endOfYear()->format('Y-m-d');
                @endphp
                <button type="button"
                        onclick="setDates('{{ $thisMonthStart }}', '{{ $thisMonthEnd }}')"
                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 transition-colors">
                    Bulan Ini
                </button>
                <button type="button"
                        onclick="setDates('{{ $last30Days }}', '{{ $today }}')"
                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 transition-colors">
                    30 Hari Terakhir
                </button>
                <button type="button"
                        onclick="setDates('{{ $thisYearStart }}', '{{ $thisYearEnd }}')"
                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 transition-colors">
                    Tahun Ini
                </button>
                <button type="button"
                        onclick="setDates('', '')"
                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 transition-colors">
                    Semua Waktu
                </button>
            </div>
        </form>
    </div>

    {{-- Metric Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Reservasi Disetujui --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reservasi Disetujui</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-slate-900">{{ $summary['total_approved_reservations'] }}</div>
                <p class="text-xs text-slate-500 mt-1">Kegiatan reservasi terlaksana</p>
            </div>
        </div>

        {{-- Total Jam Terpakai --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Jam Terpakai</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-indigo-600">{{ $summary['total_hours_used'] }} <span class="text-base font-semibold text-slate-500">Jam</span></div>
                <p class="text-xs text-slate-500 mt-1">Akumulasi durasi penggunaan</p>
            </div>
        </div>

        {{-- Total Laporan Kerusakan --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Laporan Kerusakan</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-slate-900">{{ $summary['total_reports'] }}</div>
                <div class="flex items-center space-x-1.5 text-xs text-slate-500 mt-1">
                    <span class="text-emerald-600 font-semibold">{{ $summary['total_reports_resolved'] }} selesai</span>
                    <span>•</span>
                    <span>{{ $summary['overall_resolution_rate'] }}% resolusi</span>
                </div>
            </div>
        </div>

        {{-- Total Fasilitas Terdata --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fasilitas</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-slate-900">{{ $summary['total_facilities'] }}</div>
                <p class="text-xs text-slate-500 mt-1">
                    @if($summary['top_occupancy'])
                        Top: <span class="font-medium text-slate-800">{{ $summary['top_occupancy']['facility']->nama }}</span>
                    @else
                        Belum ada pemakaian
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- BAGIAN 1: REKAP OKUPANSI PER FASILITAS --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 mr-2"></span>
                    1. Rekap Okupansi per Fasilitas
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Data akumulasi reservasi dengan status <strong>Disetujui</strong> dan total jam penggunaan fasilitas pada periode aktif.
                </p>
            </div>
            <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'occupancy'])) }}"
               class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-3 py-1.5 rounded-xl transition-colors">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh CSV Okupansi
            </a>
        </div>

        @if($occupancyData->isEmpty())
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm text-slate-500 text-sm">
                Tidak ada fasilitas terdaftar dalam sistem.
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4">Nama Fasilitas & Lokasi</th>
                                <th class="py-3.5 px-4">Tipe</th>
                                <th class="py-3.5 px-4 text-center">Kapasitas</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Reservasi Disetujui</th>
                                <th class="py-3.5 px-4 text-right">Total Jam Terpakai</th>
                                <th class="py-3.5 px-4 text-right">Rata-rata Durasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($occupancyData as $index => $row)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $row['facility']->nama }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center mt-0.5">
                                            <svg class="w-3 h-3 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $row['facility']->lokasi }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 capitalize">
                                            {{ $row['facility']->tipe }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-slate-700 font-medium">
                                        {{ $row['facility']->kapasitas }} orang
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $row['facility']->status_badge_classes }}">
                                            {{ $row['facility']->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold {{ $row['approved_count'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-500 border border-slate-200' }}">
                                            {{ $row['approved_count'] }} kali
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="font-extrabold text-sm {{ $row['total_hours'] > 0 ? 'text-indigo-600' : 'text-slate-400' }}">
                                            {{ $row['total_hours'] }} Jam
                                        </div>
                                        <div class="text-[10px] text-slate-400">({{ $row['formatted_duration'] }})</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-600 font-semibold">
                                        {{ $row['avg_hours'] > 0 ? $row['avg_hours'].' Jam' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/90 font-bold border-t-2 border-slate-200 text-slate-800 text-xs">
                            <tr>
                                <td colspan="5" class="py-3.5 px-4 text-right uppercase tracking-wider">Total Keseluruhan Okupansi:</td>
                                <td class="py-3.5 px-4 text-center text-emerald-700 font-extrabold text-sm">
                                    {{ $summary['total_approved_reservations'] }} kali
                                </td>
                                <td class="py-3.5 px-4 text-right text-indigo-700 font-extrabold text-sm">
                                    {{ $summary['total_hours_used'] }} Jam
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-500 font-medium">
                                    @php
                                        $overallAvg = $summary['total_approved_reservations'] > 0
                                            ? round($summary['total_hours_used'] / $summary['total_approved_reservations'], 2)
                                            : 0;
                                    @endphp
                                    {{ $overallAvg > 0 ? $overallAvg.' Jam/Res' : '—' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- BAGIAN 2: FREKUENSI KERUSAKAN PER FASILITAS & PER LOKASI --}}
    <div class="grid grid-cols-1 gap-8">
        {{-- 2A. Frekuensi Kerusakan per Fasilitas --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600 mr-2"></span>
                        2. Frekuensi Kerusakan per Fasilitas
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Distribusi jumlah pelaporan kerusakan per fasilitas beserta status tindak lanjut petugas pada rentang tanggal aktif.
                    </p>
                </div>
                <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'damage_facility'])) }}"
                   class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1.5 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh CSV Kerusakan Fasilitas
                </a>
            </div>

            @if($damageFacilityData->isEmpty())
                <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm text-slate-500 text-sm">
                    Belum ada data fasilitas.
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/80 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                                <tr>
                                    <th class="py-3.5 px-4 text-center w-12">No</th>
                                    <th class="py-3.5 px-4">Nama Fasilitas & Lokasi</th>
                                    <th class="py-3.5 px-4">Tipe</th>
                                    <th class="py-3.5 px-4 text-center">Total Laporan</th>
                                    <th class="py-3.5 px-3 text-center">Baru</th>
                                    <th class="py-3.5 px-3 text-center">Diproses</th>
                                    <th class="py-3.5 px-3 text-center">Selesai</th>
                                    <th class="py-3.5 px-3 text-center">Ditolak</th>
                                    <th class="py-3.5 px-4 text-center">Penyelesaian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($damageFacilityData as $index => $row)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 text-sm">{{ $row['facility']->nama }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $row['facility']->lokasi }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 capitalize">
                                                {{ $row['facility']->tipe }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-extrabold {{ $row['total_reports'] > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                                                {{ $row['total_reports'] }} Laporan
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['baru_count'] > 0 ? 'bg-blue-100 text-blue-800' : 'text-slate-400' }}">
                                                {{ $row['baru_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['diproses_count'] > 0 ? 'bg-amber-100 text-amber-800' : 'text-slate-400' }}">
                                                {{ $row['diproses_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['selesai_count'] > 0 ? 'bg-emerald-100 text-emerald-800' : 'text-slate-400' }}">
                                                {{ $row['selesai_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['ditolak_count'] > 0 ? 'bg-slate-100 text-slate-600' : 'text-slate-400' }}">
                                                {{ $row['ditolak_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($row['total_reports'] > 0)
                                                <div class="flex items-center justify-center space-x-2">
                                                    <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $row['resolution_rate'] }}%"></div>
                                                    </div>
                                                    <span class="font-bold text-[11px] text-slate-700">{{ $row['resolution_rate'] }}%</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-[11px]">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50/90 font-bold border-t-2 border-slate-200 text-slate-800 text-xs">
                                <tr>
                                    <td colspan="3" class="py-3.5 px-4 text-right uppercase tracking-wider">Total Laporan Kerusakan:</td>
                                    <td class="py-3.5 px-4 text-center text-rose-700 font-extrabold text-sm">
                                        {{ $summary['total_reports'] }} Laporan
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-blue-700 font-bold">
                                        {{ $damageFacilityData->sum('baru_count') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-amber-700 font-bold">
                                        {{ $damageFacilityData->sum('diproses_count') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-emerald-700 font-bold">
                                        {{ $damageFacilityData->sum('selesai_count') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-slate-600 font-bold">
                                        {{ $damageFacilityData->sum('ditolak_count') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-emerald-700 font-extrabold">
                                        {{ $summary['overall_resolution_rate'] }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- 2B. Frekuensi Kerusakan per Lokasi --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600 mr-2"></span>
                        3. Frekuensi Kerusakan per Lokasi
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Agregasi laporan kerusakan berdasarkan area / gedung lokasi fasilitas untuk memetakan titik kerusakan paling tinggi.
                    </p>
                </div>
                <a href="{{ route('admin.rekap.export.csv', array_merge(request()->query(), ['type' => 'damage_location'])) }}"
                   class="inline-flex items-center text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1.5 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh CSV Kerusakan Lokasi
                </a>
            </div>

            @if($damageLocationData->isEmpty())
                <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm text-slate-500 text-sm">
                    Belum ada lokasi terdata.
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/80 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                                <tr>
                                    <th class="py-3.5 px-4 text-center w-12">No</th>
                                    <th class="py-3.5 px-4">Nama Lokasi / Gedung</th>
                                    <th class="py-3.5 px-4 text-center">Jml Fasilitas</th>
                                    <th class="py-3.5 px-4">Fasilitas Terkait</th>
                                    <th class="py-3.5 px-4 text-center">Total Laporan</th>
                                    <th class="py-3.5 px-3 text-center">Baru</th>
                                    <th class="py-3.5 px-3 text-center">Diproses</th>
                                    <th class="py-3.5 px-3 text-center">Selesai</th>
                                    <th class="py-3.5 px-3 text-center">Ditolak</th>
                                    <th class="py-3.5 px-4 text-center">Perlu Penanganan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($damageLocationData as $index => $row)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                {{ $row['lokasi'] }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-semibold text-slate-700">
                                            {{ $row['facilities_count'] }} unit
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 text-[11px] max-w-xs truncate" title="{{ $row['facility_names'] }}">
                                            {{ $row['facility_names'] ?: '—' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-extrabold {{ $row['total_reports'] > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                                                {{ $row['total_reports'] }} Laporan
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['baru_count'] > 0 ? 'bg-blue-100 text-blue-800' : 'text-slate-400' }}">
                                                {{ $row['baru_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['diproses_count'] > 0 ? 'bg-amber-100 text-amber-800' : 'text-slate-400' }}">
                                                {{ $row['diproses_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['selesai_count'] > 0 ? 'bg-emerald-100 text-emerald-800' : 'text-slate-400' }}">
                                                {{ $row['selesai_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['ditolak_count'] > 0 ? 'bg-slate-100 text-slate-600' : 'text-slate-400' }}">
                                                {{ $row['ditolak_count'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($row['pending_count'] > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                                    {{ $row['pending_count'] }} Menunggu
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ✓ Bersih
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50/90 font-bold border-t-2 border-slate-200 text-slate-800 text-xs">
                                <tr>
                                    <td colspan="4" class="py-3.5 px-4 text-right uppercase tracking-wider">Total Agregat Lokasi:</td>
                                    <td class="py-3.5 px-4 text-center text-rose-700 font-extrabold text-sm">
                                        {{ $summary['total_reports'] }} Laporan
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-blue-700 font-bold">
                                        {{ $damageLocationData->sum('baru_count') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-amber-700 font-bold">
                                        {{ $damageLocationData->sum('diproses_count') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-emerald-700 font-bold">
                                        {{ $damageLocationData->sum('selesai_count') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-slate-600 font-bold">
                                        {{ $damageLocationData->sum('ditolak_count') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-amber-800 font-extrabold">
                                        {{ $damageLocationData->sum('pending_count') }} Unit
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setDates(start, end) {
        document.getElementById('start_date').value = start;
        document.getElementById('end_date').value = end;
        document.getElementById('filter-form').submit();
    }

    // Close CSV menu when clicking outside
    document.addEventListener('click', function(event) {
        var menu = document.getElementById('csv-menu');
        var button = event.target.closest('button');
        if (menu && !menu.contains(event.target) && (!button || !button.contains(event.target))) {
            menu.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
