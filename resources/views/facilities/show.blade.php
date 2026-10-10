@extends('layouts.app')

@section('title', $facility->nama)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Facility Info Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('facilities.index') }}" class="inline-flex items-center text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Fasilitas
            </a>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">
                {{ $facility->tipe }}
            </span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $facility->nama }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 flex items-center mt-1.5">
                    <svg class="w-4 h-4 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $facility->lokasi }}
                </p>
            </div>

            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $facility->status_badge_classes ?? ($facility->status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($facility->status === 'perbaikan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200')) }}">
                    @if($facility->status === 'perbaikan')
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1.5 animate-pulse"></span>
                    @elseif($facility->status === 'tersedia')
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1.5"></span>
                    @endif
                    {{ ucfirst(str_replace('_', ' ', $facility->status)) }}
                </span>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 my-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-500 font-medium block">Kapasitas</span>
                <span class="text-base font-bold text-slate-800">{{ $facility->kapasitas }} Orang</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-500 font-medium block">Tipe Fasilitas</span>
                <span class="text-base font-bold text-slate-800">{{ ucfirst($facility->tipe) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 col-span-2 sm:col-span-1">
                <span class="text-xs text-slate-500 font-medium block">Aduan Aktif</span>
                <span class="text-base font-bold text-indigo-600">{{ $facility->reports_count ?? 0 }} Aduan</span>
            </div>
        </div>

        @if($facility->deskripsi)
            <div class="mb-6">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Fasilitas</h2>
                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 text-sm text-slate-600 leading-relaxed">
                    {{ $facility->deskripsi }}
                </div>
            </div>
        @endif

        @if($facility->status === 'perbaikan')
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start space-x-2.5 mb-6">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <strong class="block font-semibold">Sedang Dalam Perbaikan</strong>
                    <span>Laporan kerusakan fasilitas ini sedang ditangani oleh tim teknis. Form reservasi dinonaktifkan sementara.</span>
                </div>
            </div>
        @elseif($facility->status === 'tidak_aktif')
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 text-slate-700 text-xs flex items-start space-x-2.5 mb-6">
                <svg class="w-5 h-5 text-slate-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.366zm1.414-1.414L6.525 5.11a6 6 0 018.366 8.366zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <strong class="block font-semibold">Fasilitas Tidak Aktif</strong>
                    <span>Fasilitas ini sedang berstatus tidak aktif. Pengajuan reservasi tidak tersedia.</span>
                </div>
            </div>
        @endif

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <a href="{{ route('reports.create', ['facility_id' => $facility->id]) }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                + Laporkan Masalah Fasilitas Ini
            </a>
        </div>
    </div>

    <!-- Section A: Ketersediaan Slot 30 Menit (07:00 - 20:00) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Jadwal & Ketersediaan Slot</h2>
                <p class="text-xs text-slate-500 mt-0.5">Operasional 07:00 - 20:00 (interval 30 menit). Menampilkan ketersediaan tanpa menampilkan data pribadi pemohon.</p>
            </div>

            <!-- Date Selector -->
            <form method="GET" action="{{ route('facilities.show', $facility->id) }}" class="flex items-center gap-2">
                <label for="filter_tanggal" class="text-xs font-semibold text-slate-600 whitespace-nowrap">Tanggal:</label>
                <input type="date"
                       id="filter_tanggal"
                       name="tanggal"
                       value="{{ $selectedDate }}"
                       min="{{ now()->format('Y-m-d') }}"
                       onchange="this.form.submit()"
                       class="text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                <button type="submit" class="px-3 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors">
                    Lihat
                </button>
            </form>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-500 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-100">
            <span>Tanggal: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong></span>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5"></span> Tersedia
                </span>
                <span class="inline-flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5"></span> Tidak tersedia
                </span>
            </div>
        </div>

        <!-- Table Slot 30 Menit -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Slot Waktu</th>
                        <th class="py-3 px-4">Durasi</th>
                        <th class="py-3 px-4 text-center">Status Ketersediaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($slots as $slot)
                        <tr class="{{ $slot['available'] ? 'hover:bg-emerald-50/30' : 'bg-slate-50/50' }} transition-colors">
                            <td class="py-2.5 px-4 font-semibold text-slate-800">
                                {{ $slot['start'] }} - {{ $slot['end'] }}
                            </td>
                            <td class="py-2.5 px-4 text-slate-500">
                                30 Menit
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                @if($slot['available'])
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="w-3 h-3 mr-1 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tidak tersedia
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section A: Form Reservasi -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Pengajuan Reservasi Fasilitas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ajukan penggunaan fasilitas dengan memilih tanggal, rentang jam, dan keperluan Anda.</p>
        </div>

        @if($facility->status === 'tersedia')
            @auth
                <form action="{{ route('reservations.store') }}" method="POST" id="formReservation" class="space-y-4">
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $facility->id }}">

                    <!-- Client-side Error Box -->
                    <div id="jsErrorAlert" class="hidden p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            <span id="jsErrorMessage"></span>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label for="res_tanggal" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Reservasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="date"
                               id="res_tanggal"
                               name="tanggal"
                               value="{{ old('tanggal', $selectedDate) }}"
                               min="{{ now()->format('Y-m-d') }}"
                               required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium @error('tanggal') border-rose-500 @enderror">
                        @error('tanggal')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="res_start_time" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jam Mulai (Kelipatan 30 Menit) <span class="text-rose-500">*</span>
                            </label>
                            <select id="res_start_time"
                                    name="start_time"
                                    required
                                    class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium @error('start_time') border-rose-500 @enderror">
                                <option value="">-- Pilih Jam Mulai --</option>
                                @foreach($timeOptions as $time)
                                    @if($time !== '20:00')
                                        <option value="{{ $time }}" {{ old('start_time') === $time ? 'selected' : '' }}>
                                            {{ $time }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('start_time')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="res_end_time" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jam Selesai (Kelipatan 30 Menit) <span class="text-rose-500">*</span>
                            </label>
                            <select id="res_end_time"
                                    name="end_time"
                                    required
                                    class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium @error('end_time') border-rose-500 @enderror">
                                <option value="">-- Pilih Jam Selesai --</option>
                                @foreach($timeOptions as $time)
                                    @if($time !== '07:00')
                                        <option value="{{ $time }}" {{ old('end_time') === $time ? 'selected' : '' }}>
                                            {{ $time }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('end_time')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Keperluan -->
                    <div>
                        <label for="res_keperluan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Keperluan / Tujuan Penggunaan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="res_keperluan"
                                  name="keperluan"
                                  rows="3"
                                  required
                                  placeholder="Contoh: Rapat kerja divisi, praktikum laboratorium, seminar mahasiswa..."
                                  class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium @error('keperluan') border-rose-500 @enderror">{{ old('keperluan') }}</textarea>
                        @error('keperluan')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                                id="btnSubmitReservation"
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                            Kirim Pengajuan Reservasi
                        </button>
                    </div>
                </form>
            @else
                <div class="p-6 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-center space-y-3">
                    <p class="text-xs sm:text-sm text-slate-600">
                        Untuk mengajukan reservasi fasilitas ini, Anda harus masuk ke akun Anda terlebih dahulu.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                            Masuk Sekarang
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-xs font-medium text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-50 rounded-xl transition-colors">
                            Daftar Akun
                        </a>
                    </div>
                </div>
            @endauth
        @else
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600 text-xs">
                @if($facility->status === 'perbaikan')
                    <p class="font-medium">Form reservasi ditutup karena fasilitas sedang dalam status <strong class="text-amber-700">Perbaikan</strong>. Silakan kembali setelah proses perbaikan selesai.</p>
                @else
                    <p class="font-medium">Form reservasi ditutup karena fasilitas sedang berstatus <strong class="text-slate-800">Tidak Aktif</strong>.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formReservation');
    if (!form) return;

    const inputTanggal = document.getElementById('res_tanggal');
    const selectStart = document.getElementById('res_start_time');
    const selectEnd = document.getElementById('res_end_time');
    const textareaKeperluan = document.getElementById('res_keperluan');
    const errorAlert = document.getElementById('jsErrorAlert');
    const errorMessage = document.getElementById('jsErrorMessage');

    function showError(msg) {
        errorMessage.textContent = msg;
        errorAlert.classList.remove('hidden');
    }

    function clearError() {
        errorAlert.classList.add('hidden');
        errorMessage.textContent = '';
    }

    // Dynamic filtering for end_time based on start_time
    selectStart.addEventListener('change', function () {
        clearError();
        const startVal = this.value;
        if (!startVal) return;

        // If end_time is currently less than or equal to startVal, reset or auto-select next slot
        if (selectEnd.value && selectEnd.value <= startVal) {
            selectEnd.value = '';
        }
    });

    selectEnd.addEventListener('change', function () {
        clearError();
        if (selectStart.value && this.value && this.value <= selectStart.value) {
            showError('Jam selesai harus lebih besar dari jam mulai.');
        }
    });

    inputTanggal.addEventListener('change', function () {
        clearError();
        const today = new Date().toISOString().split('T')[0];
        if (this.value < today) {
            showError('Tanggal reservasi tidak boleh merupakan tanggal yang telah lewat.');
        }
    });

    form.addEventListener('submit', function (e) {
        clearError();

        const tanggalVal = inputTanggal.value;
        const startVal = selectStart.value;
        const endVal = selectEnd.value;
        const keperluanVal = textareaKeperluan.value.trim();

        const today = new Date().toISOString().split('T')[0];

        if (!tanggalVal) {
            e.preventDefault();
            showError('Silakan pilih tanggal reservasi.');
            inputTanggal.focus();
            return;
        }

        if (tanggalVal < today) {
            e.preventDefault();
            showError('Tanggal reservasi tidak boleh merupakan tanggal yang telah lewat.');
            inputTanggal.focus();
            return;
        }

        if (!startVal) {
            e.preventDefault();
            showError('Silakan pilih jam mulai reservasi.');
            selectStart.focus();
            return;
        }

        if (!endVal) {
            e.preventDefault();
            showError('Silakan pilih jam selesai reservasi.');
            selectEnd.focus();
            return;
        }

        if (endVal <= startVal) {
            e.preventDefault();
            showError('Jam selesai harus lebih besar dari jam mulai (minimal durasi 30 menit).');
            selectEnd.focus();
            return;
        }

        if (!keperluanVal) {
            e.preventDefault();
            showError('Silakan isi keperluan / tujuan penggunaan fasilitas.');
            textareaKeperluan.focus();
            return;
        }
    });
});
</script>
@endpush