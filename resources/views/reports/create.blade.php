@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-4">
        <a href="{{ route('facilities.index') }}" class="hover:text-indigo-600">Fasilitas</a>
        <span>/</span>
        <a href="{{ route('reports.index') }}" class="hover:text-indigo-600">Laporan</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Buat Laporan Baru</span>
    </nav>

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8">
        <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>FR-REP-01 • Layanan Aduan Pengguna</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Laporkan Kerusakan Fasilitas</h1>
        <p class="mt-2 text-indigo-100 text-sm max-w-xl">
            Sampaikan permasalahan sarana dan prasarana agar petugas teknis dapat segera meninjau dan melakukan perbaikan.
        </p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- 1. Fasilitas Yang Bermasalah -->
            <div>
                <label for="facility_id" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Fasilitas Terkait <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select id="facility_id" name="facility_id" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all text-sm bg-white">
                        <option value="">-- Pilih Fasilitas yang Rusak / Bermasalah --</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ (old('facility_id', $selectedFacilityId) == $facility->id) ? 'selected' : '' }}>
                                {{ $facility->nama }} ({{ ucfirst($facility->tipe) }}) — {{ $facility->lokasi }} [Status: {{ $facility->status_label }}]
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="mt-1.5 text-xs text-slate-500">Pilih ruangan, laboratorium, atau area fasilitas yang mengalami gangguan.</p>
                @error('facility_id')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Kategori Kerusakan (FR-REP-01) -->
            <div>
                <label for="kategori" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Kategori Kerusakan / Masalah <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    @foreach($categories as $category)
                        <label class="cursor-pointer">
                            <input type="radio" name="kategori" value="{{ $category }}" {{ old('kategori') === $category ? 'checked' : ($loop->first && !old('kategori') ? 'checked' : '') }} class="peer sr-only">
                            <div class="px-3 py-2.5 rounded-xl border border-slate-200 text-center text-xs font-medium text-slate-700 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:font-semibold hover:bg-slate-50 transition-all">
                                {{ $category }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('kategori')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Deskripsi Kerusakan (FR-REP-01) -->
            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Deskripsi Lengkap Kerusakan <span class="text-rose-500">*</span>
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required
                          placeholder="Jelaskan secara detail permasalahan fasilitas (contoh: AC di pojok ruangan tidak berhembus dingin dan meneteskan air sejak pagi tadi)..."
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all text-sm resize-y">{{ old('deskripsi') }}</textarea>
                <div class="flex justify-between mt-1 text-xs text-slate-500">
                    <span>Minimal 5 karakter. Jelaskan lokasi spesifik atau kronologi jika perlu.</span>
                </div>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Foto Bukti Kerusakan (FR-REP-01) -->
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Foto Bukti Kerusakan <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-indigo-400 transition-colors bg-slate-50/50">
                    <div class="space-y-2 text-center">
                        <!-- Preview Container -->
                        <div id="image-preview-container" class="hidden mb-3">
                            <img id="image-preview" src="#" alt="Pratinjau Foto" class="mx-auto max-h-48 rounded-xl shadow-md object-cover border border-slate-200">
                            <button type="button" onclick="removeSelectedImage()" class="mt-2 text-xs text-rose-600 font-medium hover:underline">Hapus Foto</button>
                        </div>

                        <div id="upload-placeholder" class="text-slate-600">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600 justify-center mt-2">
                                <label for="foto" class="relative cursor-pointer bg-transparent rounded-md font-semibold text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Unggah file foto</span>
                                    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only" onchange="previewImage(this)">
                                </label>
                                <p class="pl-1 text-slate-500">atau tarik ke sini</p>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">PNG, JPG, JPEG, atau WEBP hingga 5MB</p>
                        </div>
                    </div>
                </div>
                @error('foto')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('reports.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Kirim Laporan Kerusakan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('image-preview-container');
        const placeholder = document.getElementById('upload-placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeSelectedImage() {
        const input = document.getElementById('foto');
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('image-preview-container');
        const placeholder = document.getElementById('upload-placeholder');

        input.value = '';
        preview.src = '#';
        container.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
</script>
@endpush
@endsection
