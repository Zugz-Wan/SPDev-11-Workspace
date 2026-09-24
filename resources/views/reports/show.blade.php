@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
        <a href="{{ route('facilities.index') }}" class="hover:text-indigo-600">Fasilitas</a>
        <span>/</span>
        <a href="{{ auth()->user()->isPetugas() ? route('officer.reports.index') : route('reports.index') }}" class="hover:text-indigo-600">Laporan</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Detail #REP-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
    </nav>

    <!-- Top Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-xs font-bold text-slate-400">#REP-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $report->status_badge_classes }}">
                        {{ $report->status_label }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                        {{ $report->kategori }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $report->facility->nama }}</h1>
                <p class="text-xs text-slate-500 mt-1">{{ $report->facility->lokasi }} • Pelapor: <strong>{{ $report->user->name }}</strong> ({{ $report->user->email }})</p>
            </div>

            <div class="text-right text-xs text-slate-400">
                <p>Diajukan: {{ $report->created_at->translatedFormat('d F Y, H:i') }}</p>
                <p class="mt-0.5">Pembaruan: {{ $report->updated_at->diffForHumans() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details & Notes -->
            <div class="md:col-span-2 space-y-6">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Deskripsi Kerusakan</h3>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                        {{ $report->deskripsi }}
                    </div>
                </div>

                <!-- Catatan Petugas (FR-REP-04) -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Tindak Lanjut & Catatan Petugas</h3>
                    @if($report->catatan)
                        <div class="p-4 rounded-2xl {{ $report->status === 'ditolak' ? 'bg-rose-50 border border-rose-200 text-rose-900' : 'bg-blue-50 border border-blue-200 text-blue-900' }}">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 {{ $report->status === 'ditolak' ? 'text-rose-600' : 'text-blue-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm">
                                    <strong class="block mb-1 font-bold">Catatan Penanganan:</strong>
                                    <p class="leading-relaxed">{{ $report->catatan }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-500 text-xs italic">
                            Belum ada catatan penanganan dari petugas teknis. Laporan saat ini berstatus <span class="font-semibold text-slate-700">{{ $report->status_label }}</span>.
                        </div>
                    @endif
                </div>

                <!-- Petugas Status Editor (if viewer is officer) -->
                @if(auth()->user()->isPetugas())
                    <div class="pt-6 border-t border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3">Tindakan Petugas: Ubah Status & Catatan</h3>
                        <form action="{{ route('officer.reports.update-status', $report) }}" method="POST" class="bg-purple-50/50 p-4 rounded-2xl border border-purple-100 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Laporan:</label>
                                <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white">
                                    <option value="baru" {{ $report->status === 'baru' ? 'selected' : '' }}>Baru (Antrian Masuk)</option>
                                    <option value="diproses" {{ $report->status === 'diproses' ? 'selected' : '' }}>Diproses (Sedang Ditangani)</option>
                                    <option value="selesai" {{ $report->status === 'selesai' ? 'selected' : '' }}>Selesai (Diperbaiki)</option>
                                    <option value="ditolak" {{ $report->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tindak Lanjut:</label>
                                <textarea name="catatan" rows="3" placeholder="Masukkan catatan teknis penanganan atau alasan penolakan..." class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2">{{ old('catatan', $report->catatan) }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 transition-colors">
                                Simpan Perubahan Status
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Right Col: Photo & Facility Info -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Foto Bukti Kerusakan</h3>
                    @if($report->foto)
                        <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                            <a href="{{ asset('storage/' . $report->foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $report->foto) }}" alt="Foto Kerusakan" class="w-full h-auto object-cover hover:scale-105 transition-transform">
                            </a>
                        </div>
                    @else
                        <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-400">
                            Tidak ada foto bukti yang diunggah pelapor.
                        </div>
                    @endif
                </div>

                <!-- Facility Card Snapshot -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                    <h4 class="font-bold text-slate-800">Status Ketersediaan Fasilitas</h4>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Status Saat Ini:</span>
                        <span class="px-2 py-0.5 rounded-full font-bold {{ $report->facility->status_badge_classes }}">
                            {{ $report->facility->status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kapasitas:</span>
                        <span class="font-semibold text-slate-800">{{ $report->facility->kapasitas }} Orang</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tipe:</span>
                        <span class="font-semibold uppercase text-slate-800">{{ $report->facility->tipe }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
