@extends('layouts.app')

@section('title', 'Kelola Fasilitas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Kelola Fasilitas</h1>
    <a href="{{ route('admin.facilities.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Tambah Fasilitas
    </a>
</div>

@if($facilities->count() === 0)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
        Belum ada fasilitas. Klik "Tambah Fasilitas" untuk memulai.
    </div>
@else
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Tipe</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Kapasitas</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Lokasi</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facilities as $facility)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $facility->nama }}</td>
                        <td class="px-4 py-3 text-sm">{{ ucfirst($facility->tipe) }}</td>
                        <td class="px-4 py-3 text-sm">{{ $facility->kapasitas }} orang</td>
                        <td class="px-4 py-3 text-sm">{{ $facility->lokasi }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs
                                @if($facility->status === 'tersedia') bg-green-100 text-green-700
                                @elseif($facility->status === 'perbaikan') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $facility->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                            <a href="{{ route('admin.facilities.show', $facility->id) }}"
                               class="text-blue-600 hover:underline">Lihat</a>
                            <a href="{{ route('admin.facilities.edit', $facility->id) }}"
                               class="text-yellow-600 hover:underline ml-3">Edit</a>
                            <form method="POST" action="{{ route('admin.facilities.destroy', $facility->id) }}"
                                  class="inline ml-3"
                                  onsubmit="return confirm('Yakin hapus fasilitas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $facilities->links() }}
    </div>
@endif
@endsection