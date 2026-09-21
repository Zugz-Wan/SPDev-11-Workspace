@extends('layouts.admin')

@section('title', 'Rekap Frekuensi Kerusakan - Admin')

@section('content')
<div class="space-y-8">
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-rose-600 uppercase tracking-wider mb-1">
                <span>FR-ADM-02</span>
                <span>&bull;</span>
                <span>Modul Laporan</span>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Rekap Frekuensi Kerusakan Fasilitas</h2>
            <p class="text-sm text-slate-500">Memonitor jumlah dan status pelaporan kerusakan berdasarkan fasilitas dan lokasi.</p>
        </div>

        <!-- Export Dropdown / Action Buttons (FR-ADM-03) -->
        <div class="flex items-center gap-2 flex-wrap">
            @php
                $queryParams = request()->all();
            @endphp
            <a href="{{ route('admin.rekap.kerusakan.export', array_merge($queryParams, ['format' => 'csv'])) }}" class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-300 shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.rekap.kerusakan.export', array_merge($queryParams, ['format' => 'excel'])) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('admin.rekap.kerusakan.export', array_merge($queryParams, ['format' => 'pdf'])) }}" class="inline-flex items-center px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.rekap.kerusakan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Fasilitas</label>
                <select name="facility_id" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilitiesList as $fac)
                        <option value="{{ $fac->id }}" {{ $selectedFacilityId == $fac->id ? 'selected' : '' }}>{{ $fac->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Lokasi / Gedung</label>
                <select name="lokasi" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasiList as $loc)
                        <option value="{{ $loc }}" {{ $selectedLokasi === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Status Penanganan</label>
                <select name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ $selectedStatus === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diproses" {{ $selectedStatus === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ $selectedStatus === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.rekap.kerusakan') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Laporan Kerusakan</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalReports }} Kasus</p>
            <p class="text-xs text-slate-400 mt-0.5">Dalam rentang periode terpilih</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Menunggu</p>
            <p class="text-2xl font-black text-amber-500 mt-1">{{ $totalWaiting }} Kasus</p>
            <p class="text-xs text-slate-400 mt-0.5">Belum ditangani teknisi</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Diproses</p>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ $totalInProgress }} Kasus</p>
            <p class="text-xs text-slate-400 mt-0.5">Sedang dalam perbaikan</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Selesai</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $totalDone }} Kasus</p>
            <p class="text-xs text-slate-400 mt-0.5">Fasilitas sudah kembali normal</p>
        </div>
    </div>

    <!-- Dual Table: Per Fasilitas & Per Lokasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Frekuensi per Fasilitas -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Frekuensi Kerusakan per Fasilitas</h3>
                <p class="text-xs text-slate-500">Diurutkan dari fasilitas dengan intensitas masalah terbanyak</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-3.5">Fasilitas</th>
                            <th class="py-3 px-3.5 text-center">Total</th>
                            <th class="py-3 px-3.5 text-center">Menunggu</th>
                            <th class="py-3 px-3.5 text-center">Proses</th>
                            <th class="py-3 px-3.5 text-center">Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($damageByFacility as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3.5">
                                <p class="font-bold text-slate-800">{{ $item['nama'] }}</p>
                                <p class="text-xs text-slate-400">{{ $item['lokasi'] }}</p>
                            </td>
                            <td class="py-3 px-3.5 text-center font-black text-rose-600">{{ $item['total'] }}</td>
                            <td class="py-3 px-3.5 text-center text-amber-600 font-semibold">{{ $item['menunggu'] }}</td>
                            <td class="py-3 px-3.5 text-center text-blue-600 font-semibold">{{ $item['diproses'] }}</td>
                            <td class="py-3 px-3.5 text-center text-emerald-600 font-semibold">{{ $item['selesai'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada data kerusakan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Frekuensi per Lokasi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Frekuensi Kerusakan per Lokasi / Gedung</h3>
                <p class="text-xs text-slate-500">Pemetaan konsentrasi titik kerusakan fisik gedung</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-3.5">Nama Gedung / Lokasi</th>
                            <th class="py-3 px-3.5 text-center">Total</th>
                            <th class="py-3 px-3.5 text-center">Menunggu</th>
                            <th class="py-3 px-3.5 text-center">Proses</th>
                            <th class="py-3 px-3.5 text-center">Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($damageByLocation as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3.5 font-bold text-slate-800">{{ $item['lokasi'] }}</td>
                            <td class="py-3 px-3.5 text-center font-black text-rose-600">{{ $item['total'] }}</td>
                            <td class="py-3 px-3.5 text-center text-amber-600 font-semibold">{{ $item['menunggu'] }}</td>
                            <td class="py-3 px-3.5 text-center text-blue-600 font-semibold">{{ $item['diproses'] }}</td>
                            <td class="py-3 px-3.5 text-center text-emerald-600 font-semibold">{{ $item['selesai'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada data kerusakan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Reports Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Log Rincian Laporan Kerusakan</h3>
            <p class="text-xs text-slate-500">Rincian laporan dari pengguna dan catatan status penanganan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">#</th>
                        <th class="py-3.5 px-4">Tanggal Lapor</th>
                        <th class="py-3.5 px-4">Fasilitas</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Pelapor</th>
                        <th class="py-3.5 px-4">Deskripsi Kerusakan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $index => $rep)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="py-3.5 px-4 text-slate-600 text-xs font-mono">{{ $rep->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $rep->facility->nama ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $rep->facility->lokasi ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $rep->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-slate-700 max-w-sm">{{ $rep->deskripsi }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @if($rep->status === 'menunggu')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu</span>
                            @elseif($rep->status === 'diproses')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Diproses</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-400">Tidak ada log laporan kerusakan sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
