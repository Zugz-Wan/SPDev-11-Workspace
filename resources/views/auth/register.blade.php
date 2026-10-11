@extends('layouts.app')

@section('title', 'Daftar Akun Baru - SIREFA')

@section('content')
<div class="max-w-md mx-auto my-6 sm:my-8 space-y-6">
    <!-- Header -->
    <div class="text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-lg shadow-indigo-200 mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Akun Pengguna</h1>
        <p class="text-xs text-slate-500 mt-1">Khusus Mahasiswa, Dosen, & Staf Kampus</p>
    </div>

    <!-- Notice Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs text-blue-900 leading-relaxed">
        <div class="flex items-start space-x-2">
            <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <div>
                <strong>Informasi Registrasi:</strong> Registrasi mandiri selalu menghasilkan akun dengan peran <strong>Pengguna</strong> dan berstatus <em>menunggu verifikasi admin</em>. Akun <strong>Petugas</strong> hanya dapat dibuat oleh Admin.
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Nama Lengkap Anda"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       placeholder="email@kampus.test"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                       placeholder="Minimal 8 karakter"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       placeholder="Ulangi kata sandi"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-colors">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-600">
                Sudah memiliki akun? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection