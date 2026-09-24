@extends('layouts.app')

@section('title', 'Detail Fasilitas')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <a href="{{ route('admin.facilities.index') }}" class="text-sm text-blue-600 hover:underline">
        ← Kembali
    </a>

    <h1 class="text-2xl font-bold mt-4 mb-6">{{ $facility->nama }}</h1>

    <div class="space-y-2 mb-6">
        <p><strong>Tipe:</strong> {{ ucfirst($facility->tipe) }}</p>
        <p><strong>Kapasitas:</strong> {{ $facility->kapasitas }} orang</p>
        <p><strong>Lokasi:</strong> {{ $facility->lokasi }}</p>
        <p>
            <strong>Status:</strong>
            <span class="px-2 py-1 rounded text-xs
                @if($facility->status === 'tersedia') bg-green-100 text-green-700
                @elseif($facility->status === 'perbaikan') bg-yellow-100 text-yellow-700
                @else bg-red-100 text-red-700
                @endif">
                {{ ucfirst(str_replace('_', ' ', $facility->status)) }}
            </span>
        </p>
    </div>

    @if($facility->deskripsi)
        <div class="bg-gray-50 p-4 rounded mb-6">
            <strong>Deskripsi:</strong>
            <p class="mt-1 text-gray-700">{{ $facility->deskripsi }}</p>
        </div>
    @endif

    <div class="flex gap-2">
        <a href="{{ route('admin.facilities.edit', $facility->id) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            Edit
        </a>
        <form method="POST" action="{{ route('admin.facilities.destroy', $facility->id) }}"
              onsubmit="return confirm('Yakin hapus fasilitas ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Hapus
            </button>
        </form>
    </div>
</div>
@endsection