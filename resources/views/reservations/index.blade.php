@extends('layouts.app')

@section('title', 'Reservasi Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                <span>Modul Reservasi • Riwayat Pengguna</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Reservasi Saya</h1>
            <p class="text-slate-500 text-sm mt-1">Pantau status persetujuan dan riwayat permohonan penggunaan fasilitas Anda.</p>
        </div>
        <div>
            <a href="{{ route('facilities.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajukan Reservasi Baru
            </a>
        </div>
    </div>

    <!-- Reservations Table / Content -->
    @if($reservations->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Belum Ada Reservasi</h3>
                <p class="text-slate-500 text-xs sm:text-sm max-w-sm mx-auto mt-1">
                    Anda belum pernah mengajukan reservasi fasilitas. Pilih fasilitas yang tersedia dan ajukan jadwal permohonan Anda.
                </p>
            </div>
            <div>
                <a href="{{ route('facilities.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                    Lihat Daftar Fasilitas &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Fasilitas</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Jam</th>
                            <th class="py-3.5 px-4">Keperluan</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reservations as $res)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Fasilitas -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="font-bold text-slate-900 text-sm">
                                        {{ $res->facility->nama ?? 'Fasilitas #' . $res->facility_id }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 flex items-center mt-0.5">
                                        <svg class="w-3.5 h-3.5 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $res->facility->lokasi ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        ID: #RES-{{ str_pad($res->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-slate-800">
                                        {{ \Carbon\Carbon::parse($res->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="block text-[11px] text-slate-500">
                                        {{ \Carbon\Carbon::parse($res->tanggal)->translatedFormat('l') }}
                                    </span>
                                </td>

                                <!-- Jam -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 font-semibold text-slate-700 text-xs">
                                        {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}
                                    </span>
                                </td>

                                <!-- Keperluan -->
                                <td class="py-4 px-4 max-w-xs">
                                    <p class="text-slate-700 truncate" title="{{ $res->keperluan }}">
                                        {{ $res->keperluan }}
                                    </p>
                                    @if($res->catatan_petugas)
                                        <div class="mt-1 text-[11px] text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100 truncate" title="{{ $res->catatan_petugas }}">
                                            Catatan: {{ $res->catatan_petugas }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $res->status_badge_classes }}">
                                        {{ $res->status_label }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- Detail Link -->
                                        <a href="{{ route('reservations.show', $res->id) }}"
                                           class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 border border-indigo-200 transition-colors">
                                            Detail
                                        </a>

                                        <!-- Cancel Button (Hanya pending/disetujui yang belum lewat) -->
                                        @if($res->canBeCancelled())
                                            <form action="{{ route('reservations.destroy', $res->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 border border-rose-200 transition-colors">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
