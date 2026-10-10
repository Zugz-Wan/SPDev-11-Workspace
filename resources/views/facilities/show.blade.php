@extends('layouts.app')

@section('title', $facility->nama . ' - FasilitasKita')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
        <a href="{{ route('facilities.index') }}" class="hover:text-indigo-600">Daftar Fasilitas</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">{{ $facility->nama }}</span>
    </nav>

    <!-- Facility Overview Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $facility->status_badge_classes }}">
                        @if($facility->isUnderRepair())
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1 animate-pulse"></span>
                        @elseif($facility->isAvailable())
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>
                        @endif
                        {{ $facility->status_label }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-md">
                        {{ $facility->tipe }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $facility->nama }}</h1>
                <p class="text-xs text-slate-500 flex items-center mt-1">
                    <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $facility->lokasi }}
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('reports.create', ['facility_id' => $facility->id]) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-200 transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Laporkan Masalah</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="bg-slate-50 p-3 rounded-2xl">
                <span class="text-slate-400 block text-[11px]">Kapasitas Maksimal</span>
                <span class="font-bold text-slate-800 text-sm">{{ $facility->kapasitas }} Orang</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl">
                <span class="text-slate-400 block text-[11px]">Kategori / Tipe</span>
                <span class="font-bold text-slate-800 text-sm">{{ ucfirst($facility->tipe) }}</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl">
                <span class="text-slate-400 block text-[11px]">Jam Operasional</span>
                <span class="font-bold text-slate-800 text-sm">07:00 - 20:00 WIB</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl">
                <span class="text-slate-400 block text-[11px]">Durasi Slot</span>
                <span class="font-bold text-slate-800 text-sm">30 Menit / slot</span>
            </div>
        </div>

        @if($facility->deskripsi)
            <div class="text-xs text-slate-600 leading-relaxed bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                <strong class="text-slate-800 block mb-1">Deskripsi & Fasilitas Pendukung:</strong>
                {{ $facility->deskripsi }}
            </div>
        @endif
    </div>

    <!-- Schedule & Reservation Section (2 columns on desktop) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Slot Ketersediaan Jadwal -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Jadwal Ketersediaan Slot</h2>
                    <p class="text-[11px] text-slate-500">Ketersediaan slot 30 menit per sesi penggunaan fasilitas.</p>
                </div>
            </div>

            <!-- Date selector form -->
            <form action="{{ route('facilities.show', $facility->id) }}" method="GET" class="flex items-center space-x-2">
                <label for="date-select" class="text-xs font-semibold text-slate-700 whitespace-nowrap">Tanggal:</label>
                <input type="date" id="date-select" name="date" value="{{ $selectedDate }}" min="{{ date('Y-m-d') }}"
                       onchange="this.form.submit()"
                       class="text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <noscript><button type="submit" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg">Pilih</button></noscript>
            </form>

            @if($facility->status === 'perbaikan')
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-900 flex items-center space-x-2">
                    <span>⚠️</span>
                    <span>Fasilitas ini sedang berstatus <strong>Dalam Perbaikan</strong>. Semua slot tidak tersedia untuk reservasi saat ini.</span>
                </div>
            @endif

            <!-- Slot Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                @foreach($slots as $slot)
                    <div class="p-2.5 rounded-xl border flex items-center justify-between
                        @if($facility->status === 'perbaikan') bg-amber-50/60 border-amber-200 text-amber-800
                        @elseif($slot['is_booked']) bg-rose-50 border-rose-200 text-rose-800
                        @else bg-emerald-50/70 border-emerald-200 text-emerald-800
                        @endif">
                        <span class="font-mono font-medium">{{ $slot['start'] }} - {{ $slot['end'] }}</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded
                            @if($facility->status === 'perbaikan') bg-amber-100 text-amber-800
                            @elseif($slot['is_booked']) bg-rose-100 text-rose-700
                            @else bg-emerald-100 text-emerald-700
                            @endif">
                            @if($facility->status === 'perbaikan') Perbaikan
                            @elseif($slot['is_booked']) Terisi
                            @else Tersedia
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="flex items-center space-x-4 pt-2 text-[11px] text-slate-500">
                <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded bg-emerald-400 mr-1.5"></span> Tersedia</span>
                <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded bg-rose-400 mr-1.5"></span> Terisi / Disetujui</span>
                <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded bg-amber-400 mr-1.5"></span> Dalam Perbaikan</span>
            </div>
        </div>

        <!-- Right: Form Pengajuan Reservasi -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Ajukan Reservasi</h2>
                <p class="text-[11px] text-slate-500">Pilih rentang slot waktu dan lengkapi rincian kegiatan Anda.</p>
            </div>

            @auth
                @if(auth()->user()->isPengguna())
                    @if($facility->status !== 'tersedia')
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-900">
                            Fasilitas saat ini tidak berstatus <strong>Tersedia</strong> sehingga belum dapat menerima pengajuan reservasi baru.
                        </div>
                    @else
                        <form method="POST" action="{{ url('/reservations') }}" class="space-y-3.5">
                            @csrf
                            <input type="hidden" name="facility_id" value="{{ $facility->id }}">

                            <div>
                                <label for="tanggal" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Penggunaan</label>
                                <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $selectedDate) }}" min="{{ date('Y-m-d') }}" required
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                @error('tanggal')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_time" class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai</label>
                                    <select id="start_time" name="start_time" required
                                            class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 bg-white">
                                        <option value="">Pilih</option>
                                        @foreach($timeOptions as $t)
                                            @if($t !== '20:00')
                                                <option value="{{ $t }}" {{ old('start_time') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('start_time')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_time" class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai</label>
                                    <select id="end_time" name="end_time" required
                                            class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 bg-white">
                                        <option value="">Pilih</option>
                                        @foreach($timeOptions as $t)
                                            @if($t !== '07:00')
                                                <option value="{{ $t }}" {{ old('end_time') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('end_time')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="keperluan" class="block text-xs font-semibold text-slate-700 mb-1">Keperluan / Tujuan Penggunaan</label>
                                <textarea id="keperluan" name="keperluan" rows="3" required
                                          placeholder="Contoh: Rapat koordinasi BEM tingkat fakultas"
                                          class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('keperluan') }}</textarea>
                                @error('keperluan')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow-md shadow-indigo-100 transition">
                                Kirim Pengajuan Reservasi
                            </button>
                        </form>
                    @endif
                @else
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600">
                        Anda login sebagai <strong>{{ ucfirst(auth()->user()->role) }}</strong>. Pengajuan reservasi dilakukan oleh akun Pengguna Mahasiswa / Dosen / Staf.
                    </div>
                @endif
            @else
                <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl text-center space-y-3">
                    <p class="text-xs text-slate-600">Silakan login untuk dapat mengajukan reservasi fasilitas ini.</p>
                    <a href="{{ route('login') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">
                        Masuk ke Akun
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection