@extends('layouts.app')

@section('title', 'Antrian Reservasi Fasilitas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                <span>Modul Reservasi • Dashboard Petugas & Admin</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Antrian Reservasi Fasilitas</h1>
            <p class="text-slate-500 text-sm mt-1">Verifikasi, setujui, tolak, atau batalkan permohonan penggunaan fasilitas kampus.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('officer.reports.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Antrian Laporan
                @if($newReportsCount > 0)
                    <span class="ml-1.5 px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-purple-600 text-white">
                        {{ $newReportsCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Metric Cards (Reservasi Pending & Laporan Baru) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <!-- Pending (Menunggu Persetujuan) -->
        <a href="{{ route('petugas.reservations.index', ['status' => 'pending']) }}"
           class="p-4 rounded-2xl border transition-all {{ $status === 'pending' ? 'bg-amber-50 border-amber-200 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-amber-700">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Reservasi Pending</span>
            </div>
            <div class="text-2xl font-bold text-amber-900 mt-1">{{ $pendingCount }}</div>
            <div class="text-[11px] text-amber-600/80 mt-0.5">Menunggu respon</div>
        </a>

        <!-- Disetujui -->
        <a href="{{ route('petugas.reservations.index', ['status' => 'disetujui']) }}"
           class="p-4 rounded-2xl border transition-all {{ $status === 'disetujui' ? 'bg-emerald-50 border-emerald-200 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Disetujui</span>
            </div>
            <div class="text-2xl font-bold text-emerald-900 mt-1">{{ $approvedCount }}</div>
            <div class="text-[11px] text-emerald-600/80 mt-0.5">Jadwal aktif</div>
        </a>

        <!-- Ditolak -->
        <a href="{{ route('petugas.reservations.index', ['status' => 'ditolak']) }}"
           class="p-4 rounded-2xl border transition-all {{ $status === 'ditolak' ? 'bg-rose-50 border-rose-200 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-rose-700">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Ditolak</span>
            </div>
            <div class="text-2xl font-bold text-rose-900 mt-1">{{ $rejectedCount }}</div>
            <div class="text-[11px] text-rose-600/80 mt-0.5">Permohonan ditolak</div>
        </a>

        <!-- Dibatalkan -->
        <a href="{{ route('petugas.reservations.index', ['status' => 'dibatalkan']) }}"
           class="p-4 rounded-2xl border transition-all {{ $status === 'dibatalkan' ? 'bg-slate-100 border-slate-300 ring-2 ring-slate-400/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-slate-700">
                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                <span>Dibatalkan</span>
            </div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $cancelledCount }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Batal pemohon/petugas</div>
        </a>

        <!-- Laporan Baru Indicator -->
        <a href="{{ route('officer.reports.index', ['status' => 'baru']) }}"
           class="p-4 rounded-2xl border bg-purple-50/50 border-purple-200 hover:bg-purple-50 transition-all col-span-2 sm:col-span-1">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-purple-700">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                <span>Laporan Baru</span>
            </div>
            <div class="text-2xl font-bold text-purple-900 mt-1">{{ $newReportsCount }}</div>
            <div class="text-[11px] text-purple-600/80 mt-0.5">Aduan masuk &rarr;</div>
        </a>
    </div>

    <!-- Status Tabs Filter -->
    <div class="flex items-center space-x-2 overflow-x-auto border-b border-slate-200 pb-2">
        <a href="{{ route('petugas.reservations.index', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $status === 'pending' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Menunggu Persetujuan ({{ $pendingCount }})
        </a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'disetujui']) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $status === 'disetujui' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Disetujui ({{ $approvedCount }})
        </a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'ditolak']) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $status === 'ditolak' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Ditolak ({{ $rejectedCount }})
        </a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'dibatalkan']) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $status === 'dibatalkan' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Dibatalkan ({{ $cancelledCount }})
        </a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'semua']) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $status === 'semua' || $status === '' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua ({{ $totalCount }})
        </a>
    </div>

    <!-- Table of Reservations -->
    @if($reservations->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-3">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Tidak Ada Antrian Reservasi</h3>
            <p class="text-slate-500 text-xs sm:text-sm max-w-sm mx-auto">
                Tidak ada data reservasi dengan status <strong class="text-slate-700">{{ $status }}</strong> saat ini.
            </p>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">ID & Pemohon</th>
                            <th class="py-3.5 px-4">Fasilitas</th>
                            <th class="py-3.5 px-4">Jadwal & Jam</th>
                            <th class="py-3.5 px-4">Keperluan</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reservations as $res)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- ID & Pemohon -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="font-bold text-slate-900 text-sm">
                                        {{ $res->user->name ?? 'User #' . $res->user_id }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $res->user->email ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                        #RES-{{ str_pad($res->id, 4, '0', STR_PAD_LEFT) }} • {{ $res->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- Fasilitas -->
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">
                                        {{ $res->facility->nama ?? 'Fasilitas #' . $res->facility_id }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 flex items-center mt-0.5">
                                        <svg class="w-3.5 h-3.5 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $res->facility->lokasi ?? '-' }}
                                    </div>
                                    <span class="inline-block mt-1 text-[10px] uppercase font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                        Kapasitas: {{ $res->facility->kapasitas ?? '-' }}
                                    </span>
                                </td>

                                <!-- Jadwal & Jam -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-slate-800 block">
                                        {{ \Carbon\Carbon::parse($res->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 font-semibold text-indigo-700 text-xs mt-1">
                                        {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}
                                    </span>
                                </td>

                                <!-- Keperluan -->
                                <td class="py-4 px-4 max-w-xs">
                                    <p class="text-slate-700 leading-relaxed truncate" title="{{ $res->keperluan }}">
                                        {{ $res->keperluan }}
                                    </p>
                                    @if($res->catatan_petugas)
                                        <div class="mt-1 text-[11px] text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-100 truncate" title="{{ $res->catatan_petugas }}">
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

                                <!-- Aksi Petugas -->
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- Link Detail -->
                                        <a href="{{ route('reservations.show', ['id' => $res->id, 'from_petugas' => 1]) }}"
                                           class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors"
                                           title="Lihat Detail Lengkap">
                                            Detail
                                        </a>

                                        @if($res->status === 'pending')
                                            <!-- Tombol Setujui -->
                                            <form action="{{ route('petugas.reservations.approve', $res->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Setujui pengajuan reservasi ini?');"
                                                  class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                                    Setujui
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak (Buka Modal Tolak) -->
                                            <button type="button"
                                                    onclick="openRejectModal('{{ $res->id }}', '{{ addslashes($res->facility->nama ?? 'Fasilitas') }}', '{{ addslashes($res->user->name ?? 'Pemohon') }}')"
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                                                Tolak
                                            </button>
                                        @elseif($res->status === 'disetujui')
                                            <!-- Tombol Batalkan (Buka Modal Batal Petugas - Wajib Alasan) -->
                                            <button type="button"
                                                    onclick="openCancelModal('{{ $res->id }}', '{{ addslashes($res->facility->nama ?? 'Fasilitas') }}', '{{ addslashes($res->user->name ?? 'Pemohon') }}')"
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                Batalkan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($reservations->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Modal Tolak Reservasi -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div>
            <h3 class="text-base font-bold text-slate-900" id="rejectModalTitle">Tolak Pengajuan Reservasi</h3>
            <p class="text-xs text-slate-500 mt-1" id="rejectModalSubtitle">Masukkan alasan atau catatan penolakan untuk pemohon.</p>
        </div>

        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <div>
                    <label for="reject_alasan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alasan / Catatan Penolakan:
                    </label>
                    <textarea id="reject_alasan"
                              name="catatan_petugas"
                              rows="3"
                              required
                              placeholder="Contoh: Jadwal bentrok dengan kegiatan universitas, ruangan tidak dapat digunakan..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button"
                            onclick="closeRejectModal()"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-colors">
                        Konfirmasi Tolak
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Batalkan Reservasi (Wajib Isi Alasan) -->
<div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div>
            <h3 class="text-base font-bold text-slate-900" id="cancelModalTitle">Batalkan Reservasi Disetujui</h3>
            <p class="text-xs text-slate-500 mt-1" id="cancelModalSubtitle">Petugas wajib mengisi alasan pembatalan reservasi yang telah disetujui sebelumnya.</p>
        </div>

        <form id="cancelForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <div>
                    <label for="cancel_alasan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alasan Pembatalan <span class="text-rose-500">* (Wajib diisi)</span>:
                    </label>
                    <textarea id="cancel_alasan"
                              name="alasan_pembatalan"
                              rows="3"
                              required
                              placeholder="Contoh: Terjadi gangguan teknis darurat pada fasilitas, dipinjam pimpinan..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button"
                            onclick="closeCancelModal()"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition-colors">
                        Konfirmasi Pembatalan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectModal(id, facility, user) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const title = document.getElementById('rejectModalTitle');
    const subtitle = document.getElementById('rejectModalSubtitle');

    form.action = '/petugas/reservations/' + id + '/reject';
    title.textContent = 'Tolak Reservasi #' + id + ' (' + facility + ')';
    subtitle.textContent = 'Pemohon: ' + user + '. Masukkan alasan penolakan.';

    document.getElementById('reject_alasan').value = '';
    modal.classList.remove('hidden');
    document.getElementById('reject_alasan').focus();
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openCancelModal(id, facility, user) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    const title = document.getElementById('cancelModalTitle');
    const subtitle = document.getElementById('cancelModalSubtitle');

    form.action = '/petugas/reservations/' + id + '/cancel';
    title.textContent = 'Batalkan Reservasi #' + id + ' (' + facility + ')';
    subtitle.textContent = 'Pemohon: ' + user + '. Wajib memberikan alasan pembatalan resmi.';

    document.getElementById('cancel_alasan').value = '';
    modal.classList.remove('hidden');
    document.getElementById('cancel_alasan').focus();
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
        closeCancelModal();
    }
});
</script>
@endpush
