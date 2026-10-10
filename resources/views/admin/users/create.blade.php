@extends('layouts.app')

@section('title', 'Buat Akun Baru')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    {{-- Back nav --}}
    <div>
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Akun
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <div class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                Admin Panel
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Buat Akun Baru</h1>
            <p class="text-xs text-slate-500 mt-1">
                Akun yang dibuat admin langsung berstatus <strong>terverifikasi</strong> dan dapat langsung login.
                Admin hanya dapat membuat akun dengan role <strong>pengguna</strong> atau <strong>petugas</strong>.
            </p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       placeholder="Contoh: Budi Santoso"
                       class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('name') border-rose-400 @enderror">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       placeholder="contoh@domain.com"
                       class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('email') border-rose-400 @enderror">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Password <span class="text-rose-500">*</span>
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       minlength="8"
                       placeholder="Minimal 8 karakter"
                       class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('password') border-rose-400 @enderror">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Role Akun <span class="text-rose-500">*</span>
                </label>
                <select id="role"
                        name="role"
                        required
                        class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('role') border-rose-400 @enderror">
                    <option value="">-- Pilih Role --</option>
                    <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                </select>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Akun admin tidak dapat dibuat melalui form ini.
                </p>
            </div>

            {{-- Info Banner --}}
            <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Akun yang dibuat admin langsung berstatus <strong>terverifikasi</strong> dan dapat login segera tanpa persetujuan tambahan.</span>
            </div>

            {{-- Submit --}}
            <div class="pt-2 flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition-colors">
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
