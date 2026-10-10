@extends('layouts.app')

@section('title', 'Tambah Akun Langsung - Admin')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:underline font-medium inline-flex items-center mb-1">
                ← Kembali ke Manajemen Akun
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Akun Langsung</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pendaftaran akun Petugas Fasilitas atau Pengguna oleh Admin.</p>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Contoh: Budi Santoso"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       placeholder="Contoh: budi@kampus.ac.id"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                <input type="password" id="password" name="password" required
                       placeholder="Minimal 6 karakter"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">Peran / Role <span class="text-rose-500">*</span></label>
                <select id="role" name="role" required
                        class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 bg-white">
                    <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna (Mahasiswa / Dosen / Staf)</option>
                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas (Fasilitas & Penanganan Kerusakan)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh)</option>
                </select>
                @error('role')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-100 transition">
                    Daftarkan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
