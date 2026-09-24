@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                <span>FR-REP-02 • Riwayat Aduan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Status Laporan Saya</h1>
            <p class="text-slate-500 text-sm mt-1">Pantau perkembangan tindak lanjut dan catatan teknisi dari laporan kerusakan fasilitas yang telah diajukan.</p>
        </div>
        <div>
            <a href="{{ route('reports.create') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Lapor Kerusakan Baru</span>
            </a>
        </div>
    </div>

    <!-- Status Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <a href="{{ route('reports.index') }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === '' ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold text-slate-500">Semua Laporan</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['total'] }}</div>
        </a>
        <a href="{{ route('reports.index', ['status' => 'baru']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'baru' ? 'bg-blue-50 border-blue-200 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-blue-700">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Baru</span>
            </div>
            <div class="text-2xl font-bold text-blue-900 mt-1">{{ $counts['baru'] }}</div>
        </a>
        <a href="{{ route('reports.index', ['status' => 'diproses']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'diproses' ? 'bg-amber-50 border-amber-200 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-amber-700">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Diproses</span>
            </div>
            <div class="text-2xl font-bold text-amber-900 mt-1">{{ $counts['diproses'] }}</div>
        </a>
        <a href="{{ route('reports.index', ['status' => 'selesai']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'selesai' ? 'bg-emerald-50 border-emerald-200 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Selesai</span>
            </div>
            <div class="text-2xl font-bold text-emerald-900 mt-1">{{ $counts['selesai'] }}</div>
        </a>
        <a href="{{ route('reports.index', ['status' => 'ditolak']) }}" class="p-4 rounded-2xl border transition-all {{ $currentStatus === 'ditolak' ? 'bg-rose-50 border-rose-200 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }} col-span-2 sm:col-span-1">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-rose-700">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Ditolak</span>
            </div>
            <div class="text-2xl font-bold text-rose-900 mt-1">{{ $counts['ditolak'] }}</div>
        </a>
    </div>

    <!-- Reports List / Table -->
    @if($reports->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Belum Ada Laporan</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto mt-1 mb-6">
                @if($currentStatus !== '')
                    Tidak ditemukan laporan dengan filter status "{{ ucfirst($currentStatus) }}".
                @else
                    Anda belum pernah mengajukan laporan kerusakan fasilitas.
                @endif
            </p>
            <a href="{{ route('reports.create') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                Ajukan Laporan Sekarang
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                        <!-- Left Details -->
                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-bold text-slate-400">#REP-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $report->status_badge_classes }}">
                                    {{ $report->status_label }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $report->kategori }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    • Diajukan {{ $report->created_at->diffForHumans() }} ({{ $report->created_at->translatedFormat('d M Y, H:i') }})
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                                <span>{{ $report->facility->nama }}</span>
                                <span class="text-xs font-normal text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $report->facility->lokasi }}
                                </span>
                            </h3>

                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                {{ $report->deskripsi }}
                            </p>

                            <!-- Catatan Petugas (FR-REP-04) -->
                            @if($report->catatan)
                                <div class="mt-3 p-3.5 rounded-xl {{ $report->status === 'ditolak' ? 'bg-rose-50 border border-rose-200 text-rose-900' : 'bg-blue-50/80 border border-blue-200 text-blue-900' }}">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 {{ $report->status === 'ditolak' ? 'text-rose-600' : 'text-blue-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="text-xs">
                                            <span class="font-bold block mb-0.5">Catatan Petugas Teknis:</span>
                                            <span>{{ $report->catatan }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Photo Thumbnail (if uploaded) -->
                        @if($report->foto)
                            <div class="lg:w-44 flex-shrink-0">
                                <a href="{{ asset('storage/' . $report->foto) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 shadow-sm aspect-video bg-slate-100">
                                    <img src="{{ asset('storage/' . $report->foto) }}" alt="Foto Kerusakan" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold">
                                        Lihat Foto
                                    </div>
                                </a>
                                <p class="text-[10px] text-center text-slate-400 mt-1">Klik untuk memperbesar</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-4">
                {{ $reports->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
