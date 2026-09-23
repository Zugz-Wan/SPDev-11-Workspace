@extends('layouts.app')

@section('title', 'Tambah Fasilitas')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <a href="{{ route('admin.facilities.index') }}" class="text-sm text-blue-600 hover:underline">
        ← Kembali
    </a>
    <h1 class="text-2xl font-bold mt-4 mb-6">Tambah Fasilitas</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.facilities.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Fasilitas</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Tipe</label>
            <select name="tipe" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Tipe --</option>
                <option value="ruangan" {{ old('tipe') === 'ruangan' ? 'selected' : '' }}>Ruangan</option>
                <option value="lapangan" {{ old('tipe') === 'lapangan' ? 'selected' : '' }}>Lapangan</option>
                <option value="aula" {{ old('tipe') === 'aula' ? 'selected' : '' }}>Aula</option>
                <option value="laboratorium" {{ old('tipe') === 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Kapasitas (orang)</label>
            <input type="number" name="kapasitas" value="{{ old('kapasitas') }}" min="1" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" required class="w-full border rounded px-3 py-2">
                <option value="tersedia" {{ old('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="perbaikan" {{ old('status') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                <option value="tidak_aktif" {{ old('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Deskripsi (opsional)</label>
            <textarea name="deskripsi" rows="3"
                class="w-full border rounded px-3 py-2">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan
            </button>
            <a href="{{ route('admin.facilities.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection