@extends('layouts.app')

@section('title', 'Detail Reservasi #' . str_pad($reservation->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ auth()->user()->isPetugas() && request()->has('from_petugas') ? route('petugas.reservations.index') : route('reservations.my') }}"
           class="inline-flex items-center text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke {{ auth()->user()->isPetugas() && request()->has('from_petugas') ? 'Antrian Reservasi' : 'Daftar Reservasi' }}
        </a>
        <span class="text-xs font-mono font-semibold text-slate-400">
            #RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
        </span>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block mb-1">Detail Informasi Reservasi</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $reservation->facility->nama ?? 'Fasilitas #' . $reservation->facility_id }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 flex items-center mt-1">
                    <svg class="w-4 h-4 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $reservation->facility->lokasi ?? '-' }}
                </p>
            </div>

            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $reservation->status_badge_classes }}">
                    {{ $reservation->status_label }}
                </span>
            </div>
        </div>

        <!-- Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-xs text-slate-500 font-medium block">Tanggal Pelaksanaan</span>
                <span class="text-sm sm:text-base font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($reservation->tanggal)->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-xs text-slate-500 font-medium block">Rentang Jam Penggunaan</span>
                <span class="text-sm sm:text-base font-bold text-slate-800">
                    {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} WIB
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-xs text-slate-500 font-medium block">Pemohon</span>
                <span class="text-sm font-bold text-slate-800">
                    {{ $reservation->user->name ?? '-' }}
                </span>
                <span class="text-xs text-slate-500 block">
                    {{ $reservation->user->email ?? '-' }}
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-xs text-slate-500 font-medium block">Waktu Pengajuan</span>
                <span class="text-sm font-semibold text-slate-800">
                    {{ $reservation->created_at->translatedFormat('d M Y, H:i') }}
                </span>
                <span class="text-xs text-slate-400 block">
                    ({{ $reservation->created_at->diffForHumans() }})
                </span>
            </div>
        </div>

        <!-- Tujuan / Keperluan -->
        <div>
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tujuan / Keperluan Penggunaan</h2>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $reservation->keperluan }}
            </div>
        </div>

        <!-- Catatan Petugas (Jika Ada) -->
        @if($reservation->catatan_petugas)
            <div>
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan / Alasan dari Petugas</h2>
                <div class="p-4 rounded-2xl {{ $reservation->status === 'ditolak' || $reservation->status === 'dibatalkan' ? 'bg-rose-50 border border-rose-200 text-rose-900' : 'bg-purple-50 border border-purple-200 text-purple-900' }} text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                    {{ $reservation->catatan_petugas }}
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <a href="{{ auth()->user()->isPetugas() && request()->has('from_petugas') ? route('petugas.reservations.index') : route('reservations.my') }}"
               class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors text-center">
                &larr; Kembali
            </a>

            @if($reservation->canBeCancelled() && $reservation->user_id === auth()->id())
                <form action="{{ route('reservations.destroy', $reservation->id) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan reservasi ini?');"
                      class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-sm transition-colors text-center">
                        Batalkan Reservasi Ini
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
