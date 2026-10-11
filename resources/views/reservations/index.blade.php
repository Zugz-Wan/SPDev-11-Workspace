@extends('layouts.app')

@section('title', 'Status & Riwayat Reservasi Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Status & Riwayat Reservasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau proses verifikasi dan persetujuan pengajuan peminjaman fasilitas kampus Anda.
            </p>
        </div>
        <div>
            <a href="{{ route('facilities.index') }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajukan Reservasi Baru
            </a>
        </div>
    </div>

    <!-- Reservations List -->
    @if($reservations->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-3">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                📅
            </div>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Pengajuan Reservasi</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Anda belum pernah mengajukan reservasi ruangan, aula, atau laboratorium kampus.
            </p>
            <div class="pt-2">
                <a href="{{ route('facilities.index') }}" class="inline-block px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    Lihat Daftar Fasilitas & Ajukan
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($reservations as $res)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                    <div class="p-6 space-y-4">
                        <!-- Top status badge & ID -->
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($res->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($res->status === 'disetujui') bg-emerald-100 text-emerald-800
                                @elseif($res->status === 'ditolak') bg-rose-100 text-rose-800
                                @else bg-slate-100 text-slate-600
                                @endif">
                                {{ ucfirst($res->status) }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">#RES-{{ $res->id }}</span>
                        </div>

                        <!-- Facility Name -->
                        <div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $res->facility->nama }}</h3>
                            <p class="text-xs text-slate-500 flex items-center mt-1">
                                <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $res->facility->lokasi }}
                            </p>
                        </div>

                        <!-- Booking Details -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Tanggal:</span>
                                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($res->tanggal)->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Waktu:</span>
                                <span class="font-semibold text-slate-800">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} WIB</span>
                            </div>
                            <div class="pt-1.5 border-t border-slate-200/60">
                                <span class="text-slate-400 block mb-0.5">Keperluan:</span>
                                <p class="text-slate-700 italic leading-relaxed">{{ $res->keperluan }}</p>
                            </div>
                        </div>

                        <!-- Catatan Petugas if any -->
                        @if($res->catatan_petugas)
                            <div class="p-3 bg-amber-50/70 border border-amber-200/60 rounded-xl text-xs text-amber-900">
                                <span class="font-semibold block mb-0.5">Catatan Petugas:</span>
                                {{ $res->catatan_petugas }}
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Action (Cancel option if pending/approved) -->
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-400">Diajukan {{ $res->created_at->diffForHumans() }}</span>

                        @if(($res->status === 'pending' || $res->status === 'disetujui') && $res->canBeCancelled())
                            <form method="POST" action="{{ route('reservations.destroy', $res->id) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan reservasi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold hover:underline">
                                    Batalkan Reservasi
                                </button>
                            </form>
                        @elseif($res->status === 'pending' || $res->status === 'disetujui')
                            <span class="text-slate-400 text-xs italic" title="Pembatalan ditutup karena waktu mulai kurang dari 2 jam dari sekarang atau sudah lewat">
                                Batas pembatalan lewat (&lt; 2 jam)
                            </span>
                        @else
                            <span class="text-slate-400 text-xs">-</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
