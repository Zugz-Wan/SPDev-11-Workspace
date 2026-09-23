@extends('layouts.app')

@section('title', 'Daftar Fasilitas')

@section('content')
<h1 class="text-3xl font-bold mb-6">Daftar Fasilitas Kampus</h1>

@if($facilities->count() === 0)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
        Belum ada fasilitas tersedia.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($facilities as $facility)
            <div class="bg-white rounded shadow p-4 hover:shadow-lg transition">
                <h2 class="text-xl font-bold mb-2">{{ $facility->nama }}</h2>
                <p class="text-sm text-gray-600 mb-1">
                    <strong>Tipe:</strong> {{ ucfirst($facility->tipe) }}
                </p>
                <p class="text-sm text-gray-600 mb-1">
                    <strong>Kapasitas:</strong> {{ $facility->kapasitas }} orang
                </p>
                <p class="text-sm text-gray-600 mb-3">
                    <strong>Lokasi:</strong> {{ $facility->lokasi }}
                </p>
                <a href="{{ route('facilities.show', $facility->id) }}"
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Lihat Detail
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $facilities->links() }}
    </div>
@endif
@endsection