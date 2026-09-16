@extends('layouts.app')

@section('title', $facility->nama)

@section('content')
<div class="bg-white rounded shadow p-6 max-w-3xl mx-auto">
    <a href="{{ route('facilities.index') }}" class="text-sm text-blue-600 hover:underline">
        ← Kembali ke daftar
    </a>

    <h1 class="text-3xl font-bold mt-4 mb-4">{{ $facility->nama }}</h1>

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

    @auth
        @if($facility->status === 'tersedia')
            <div class="bg-blue-50 border border-blue-200 p-4 rounded">
                <p class="text-sm text-blue-700">
                    Ingin reservasi fasilitas ini? Fitur reservasi akan datang di modul berikutnya.
                </p>
            </div>
        @endif
    @else
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded">
            <p class="text-sm text-yellow-700">
                <a href="{{ route('login') }}" class="underline">Login</a> untuk melakukan reservasi.
            </p>
        </div>
    @endauth
</div>
@endsection