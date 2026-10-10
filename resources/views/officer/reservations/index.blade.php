@extends('layouts.app')

@section('title', 'Antrian Reservasi - Petugas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Antrian Reservasi Fasilitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Verifikasi, setujui, tolak, atau batalkan jadwal reservasi fasilitas kampus.
            </p>
        </div>
    </div>

    <!-- Quick Status KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Reservasi Pending</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⏳
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Reservasi Disetujui</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $approvedCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Reservasi Terdaftar</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $reservations->total() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                📋
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ url('/petugas/reservations') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari fasilitas, pemohon, tujuan..."
                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Status Reservasi</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                    Filter Antrian
                </button>
                <a href="{{ url('/petugas/reservations') }}" class="px-3 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Fasilitas</th>
                        <th class="px-4 py-3.5">Pemohon</th>
                        <th class="px-4 py-3.5">Jadwal & Waktu</th>
                        <th class="px-4 py-3.5">Tujuan / Keperluan</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Tindakan Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservations as $res)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $res->status === 'pending' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-4 py-3.5 font-bold text-slate-900">
                                {{ $res->facility->nama }}
                                <span class="block text-[11px] font-normal text-slate-500">{{ $res->facility->lokasi }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-800">{{ $res->user->name }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $res->user->email }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($res->tanggal)->translatedFormat('l, d M Y') }}
                                </div>
                                <div class="text-xs text-indigo-700 font-mono font-medium">
                                    {{ substr($res->start_time, 0, 5) }} – {{ substr($res->end_time, 0, 5) }} WIB
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-700 max-w-xs">
                                <p class="line-clamp-2 text-xs leading-relaxed">{{ $res->keperluan }}</p>
                                @if($res->catatan_petugas)
                                    <div class="mt-1 text-[11px] text-slate-500 italic bg-slate-50 p-1.5 rounded border border-slate-100">
                                        Catatan: {{ $res->catatan_petugas }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($res->status === 'pending') bg-amber-100 text-amber-800
                                    @elseif($res->status === 'disetujui') bg-emerald-100 text-emerald-800
                                    @elseif($res->status === 'ditolak') bg-rose-100 text-rose-800
                                    @else bg-slate-100 text-slate-600
                                    @endif">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                @if($res->status === 'pending')
                                    <!-- Approve -->
                                    <form method="POST" action="{{ url("/petugas/reservations/{$res->id}/approve") }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            ✓ Setujui
                                        </button>
                                    </form>

                                    <!-- Reject with reason prompt -->
                                    <form method="POST" action="{{ url("/petugas/reservations/{$res->id}/reject") }}" class="inline"
                                          onsubmit="var r = prompt('Masukkan alasan penolakan reservasi:'); if (r === null) return false; this.catatan_petugas.value = r; return true;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="catatan_petugas" value="">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition ml-1">
                                            ✕ Tolak
                                        </button>
                                    </form>
                                @elseif($res->status === 'disetujui')
                                    <!-- Emergency Cancel (US 10) -->
                                    <form method="POST" action="{{ url("/petugas/reservations/{$res->id}/cancel") }}" class="inline"
                                          onsubmit="var r = prompt('Kondisi mendesak: Masukkan alasan pembatalan reservasi yang telah disetujui:'); if (!r) { alert('Alasan pembatalan wajib diisi.'); return false; } this.alasan_pembatalan.value = r; return true;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="alasan_pembatalan" value="">
                                        <button type="submit" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold transition">
                                            ⚠️ Batalkan (Darurat)
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Selesai diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                Tidak ada data reservasi pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection
