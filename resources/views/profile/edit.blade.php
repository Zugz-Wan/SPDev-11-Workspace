@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
        <a href="{{ route('facilities.index') }}" class="hover:text-indigo-600">Beranda</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Profil Saya</span>
    </nav>

    <!-- Profile Header Hero -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-md shadow-indigo-900/50 border border-indigo-400/30">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $user->role === 'petugas' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : ($user->role === 'admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ ($user->status_akun ?? 'terverifikasi') === 'terverifikasi' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }} flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ ($user->status_akun ?? 'terverifikasi') === 'terverifikasi' ? 'bg-emerald-400' : 'bg-amber-400' }} animate-pulse"></span>
                            {{ ucfirst($user->status_akun ?? 'terverifikasi') }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">{{ $user->name }}</h1>
                    <p class="text-xs text-slate-300 mt-0.5">{{ $user->email }}</p>
                </div>
            </div>

            <div class="text-xs text-slate-400 sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-800">
                <p>Terdaftar sejak: <strong class="text-slate-200">{{ $user->created_at->translatedFormat('d F Y') }}</strong></p>
                <p class="mt-0.5">Terakhir login: <strong class="text-slate-200">{{ now()->translatedFormat('d M Y, H:i') }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Forms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. Edit Profile Form -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Informasi Pribadi</h2>
                        <p class="text-xs text-slate-500">Perbarui nama lengkap akun Anda.</p>
                    </div>
                </div>

                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all text-slate-800">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email (Readonly / Retained) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700">
                                Alamat Email
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium bg-slate-100 px-2 py-0.5 rounded-md">Terkunci / Tetap</span>
                        </div>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full text-sm rounded-xl border border-slate-200 px-4 py-2.5 bg-slate-100 text-slate-500 cursor-not-allowed select-none">
                        <p class="mt-1.5 text-[11px] text-slate-400 leading-relaxed">
                            Alamat email terdaftar tidak dapat diubah demi keamanan dan integritas riwayat pengajuan fasilitas.
                        </p>
                    </div>

                    <!-- Role & Status Info -->
                    <div class="pt-2">
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 flex items-center justify-between text-xs text-slate-600">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Peran Pengguna</span>
                                <span class="font-bold text-slate-800">{{ ucfirst($user->role) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 block text-[11px]">Status Akun</span>
                                <span class="font-bold text-emerald-600">{{ ucfirst($user->status_akun ?? 'terverifikasi') }}</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="pt-5 mt-4 border-t border-slate-100">
                <button type="submit" form="profile-form" class="w-full py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all">
                    Simpan Perubahan Nama
                </button>
            </div>
        </div>

        <!-- 2. Change Password Form -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Ganti Kata Sandi</h2>
                        <p class="text-xs text-slate-500">Perbarui kata sandi untuk keamanan akun Anda.</p>
                    </div>
                </div>

                <form id="password-form" action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                               placeholder="Masukkan kata sandi lama"
                               class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all text-slate-800">
                        @error('current_password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                               placeholder="Minimal 6 karakter"
                               class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all text-slate-800">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Ulangi kata sandi baru"
                               class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all text-slate-800">
                    </div>
                </form>
            </div>

            <div class="pt-5 mt-4 border-t border-slate-100">
                <button type="submit" form="password-form" class="w-full py-2.5 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-100 transition-all">
                    Perbarui Kata Sandi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
