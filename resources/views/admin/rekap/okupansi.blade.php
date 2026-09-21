@extends('layouts.admin')

@section('title', 'Rekap Okupansi Fasilitas - Admin')

@section('content')
<div class="space-y-8">
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                <span>FR-ADM-01</span>
                <span>&bull;</span>
                <span>Modul Laporan</span>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Rekap Okupansi Fasilitas</h2>
            <p class="text-sm text-slate-500">Menganalisis intensitas penggunaan dan jam pemakaian fasilitas lintas periode.</p>
        </div>

        <!-- Export Dropdown / Action Buttons (FR-ADM-03) -->
        <div class="flex items-center gap-2 flex-wrap">
            @php
                $queryParams = request()->all();
            @endphp
            <a href="{{ route('admin.rekap.okupansi.export', array_merge($queryParams, ['format' => 'csv'])) }}" class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-300 shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.rekap.okupansi.export', array_merge($queryParams, ['format' => 'excel'])) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('admin.rekap.okupansi.export', array_merge($queryParams, ['format' => 'pdf'])) }}" class="inline-flex items-center px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.rekap.okupansi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipe Fasilitas</label>
                <select name="tipe" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tipe</option>
                    @foreach($tipeOptions as $opt)
                        <option value="{{ $opt }}" {{ $selectedTipe === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Fasilitas Spesifik</label>
                <select name="facility_id" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilitiesList as $fac)
                        <option value="{{ $fac->id }}" {{ $selectedFacilityId == $fac->id ? 'selected' : '' }}>{{ $fac->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.rekap.okupansi') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Reservasi Disetujui</p>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ $totalApprovedReservations }} Reservasi</p>
            <p class="text-xs text-slate-400 mt-0.5">Selama periode {{ $daysDiff }} hari</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Durasi Penggunaan</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $totalHoursAll }} Jam</p>
            <p class="text-xs text-slate-400 mt-0.5">Kumulatif seluruh fasilitas terpilih</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fasilitas Paling Padat</p>
            @if(count($occupancySummary) > 0 && $occupancySummary[0]['total_hours'] > 0)
                <p class="text-lg font-black text-slate-900 mt-1 truncate">{{ $occupancySummary[0]['facility']->nama }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $occupancySummary[0]['total_hours'] }} Jam ({{ $occupancySummary[0]['occupancy_rate'] }}% okupansi)</p>
            @else
                <p class="text-lg font-black text-slate-400 mt-1">-</p>
                <p class="text-xs text-slate-400 mt-0.5">Belum ada jam tercatat</p>
            @endif
        </div>
    </div>

    <!-- Aggregated Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tabel Ringkasan Okupansi per Fasilitas</h3>
                <p class="text-xs text-slate-500">Kalkulasi durasi pemakaian dibanding kapasitas operasional 12 jam/hari.</p>
            </div>
            <span class="text-xs font-medium text-slate-400">{{ count($occupancySummary) }} Fasilitas ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">#</th>
                        <th class="py-3.5 px-4">Nama Fasilitas</th>
                        <th class="py-3.5 px-4">Tipe</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4 text-center">Kapasitas</th>
                        <th class="py-3.5 px-4 text-center">Jml Reservasi</th>
                        <th class="py-3.5 px-4 text-center">Total Durasi</th>
                        <th class="py-3.5 px-4 w-44">Tingkat Okupansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($occupancySummary as $index => $item)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">{{ $item['facility']->nama }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-700 uppercase">
                                {{ $item['facility']->tipe }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $item['facility']->lokasi }}</td>
                        <td class="py-3.5 px-4 text-center text-slate-600">{{ $item['facility']->kapasitas }} Org</td>
                        <td class="py-3.5 px-4 text-center font-bold text-indigo-600">{{ $item['total_reservations'] }}x</td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-800">{{ $item['total_hours'] }} Jam</td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $item['occupancy_rate']) }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700 w-10 text-right">{{ $item['occupancy_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-400">Tidak ada data fasilitas yang cocok dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Reservation Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Log Rincian Reservasi Disetujui</h3>
            <p class="text-xs text-slate-500">Daftar kegiatan penggunaan fasilitas dalam rentang tanggal terpilih.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">#</th>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Fasilitas</th>
                        <th class="py-3.5 px-4">Peminjam</th>
                        <th class="py-3.5 px-4">Keperluan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservations as $index => $res)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">{{ \Carbon\Carbon::parse($res->tanggal)->format('d M Y') }}</td>
                        <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}</td>
                        <td class="py-3.5 px-4 font-bold text-indigo-700">{{ $res->facility->nama ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $res->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate">{{ $res->keperluan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-400">Tidak ada riwayat reservasi pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
