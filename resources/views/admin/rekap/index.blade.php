@extends('layouts.admin')

@section('title', 'Dashboard Rekap Eksekutif - Admin')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Executive Rekap Dashboard</h2>
            <p class="text-sm text-slate-500 mt-1">Ringkasan analitik okupansi penggunaan fasilitas dan frekuensi laporan kerusakan.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.rekap.okupansi') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Lihat Rekap Okupansi
            </a>
            <a href="{{ route('admin.rekap.kerusakan') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Lihat Rekap Kerusakan
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Fasilitas Terdaftar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fasilitas</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalFacilities }} Unit</p>
            </div>
        </div>

        <!-- Total Reservasi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reservasi Disetujui</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $approvedReservations }} <span class="text-xs font-normal text-slate-400">/ {{ $totalReservations }} total</span></p>
            </div>
        </div>

        <!-- Kerusakan Menunggu -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Laporan Diproses/Menunggu</p>
                <p class="text-2xl font-black text-amber-600 mt-0.5">{{ $waitingReports + $inProgressReports }} <span class="text-xs font-normal text-slate-400">kasus</span></p>
            </div>
        </div>

        <!-- Kerusakan Terselesaikan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kerusakan Selesai</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $doneReports }} <span class="text-xs font-normal text-slate-400">kasus</span></p>
            </div>
        </div>
    </div>

    <!-- Highlight Section: Top Occupancy & Top Damages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Fasilitas Terpopuler (Okupansi) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Top Fasilitas Paling Aktif</h3>
                    <p class="text-xs text-slate-500">Berdasarkan frekuensi reservasi yang disetujui</p>
                </div>
                <a href="{{ route('admin.rekap.okupansi') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Detail Rekap &rarr;</a>
            </div>

            <div class="space-y-4">
                @forelse($topOccupancy as $facility)
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">{{ $facility->nama }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md bg-white border border-slate-200 text-slate-600 uppercase">{{ $facility->tipe }}</span>
                            <span class="text-xs text-slate-400">{{ $facility->lokasi }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                            {{ $facility->reservations_count }} Reservasi
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-6">Belum ada data reservasi tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Fasilitas Sering Kerusakan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Top Frekuensi Kerusakan</h3>
                    <p class="text-xs text-slate-500">Fasilitas yang paling sering menerima laporan masalah</p>
                </div>
                <a href="{{ route('admin.rekap.kerusakan') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700">Detail Rekap &rarr;</a>
            </div>

            <div class="space-y-4">
                @forelse($topDamage as $facility)
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">{{ $facility->nama }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md bg-white border border-slate-200 text-slate-600 uppercase">{{ $facility->tipe }}</span>
                            <span class="text-xs text-slate-400">{{ $facility->lokasi }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            {{ $facility->reports_count }} Kasus
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-6">Tidak ada riwayat kerusakan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
