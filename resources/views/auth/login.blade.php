@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-6 sm:my-10 space-y-6">
    <!-- Header -->
    <div class="text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-lg shadow-indigo-200 mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk ke FasilitasKita</h1>
        <p class="text-xs text-slate-500 mt-1">Sistem Layanan Pengaduan Kerusakan & Monitoring Fasilitas</p>
    </div>

    <!-- Quick Login Cards (Demo Shortcut) -->
    <div class="bg-gradient-to-br from-indigo-50/70 to-purple-50/70 p-5 rounded-2xl border border-indigo-100/80 shadow-sm space-y-2.5">
        <div class="flex items-center space-x-1.5 text-xs font-bold text-indigo-900 mb-1">
            <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
            <span>One-Click Demo Login:</span>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('quick-login', 'pengguna') }}" class="p-3 bg-white rounded-xl border border-blue-200 hover:border-blue-400 hover:bg-blue-50/50 shadow-sm transition-all text-left group">
                <div class="text-xs font-bold text-blue-700 group-hover:text-blue-800 flex items-center justify-between">
                    <span>Pengguna</span>
                    <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">User</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">user@example.com</p>
            </a>

            <a href="{{ route('quick-login', 'petugas') }}" class="p-3 bg-white rounded-xl border border-purple-200 hover:border-purple-400 hover:bg-purple-50/50 shadow-sm transition-all text-left group">
                <div class="text-xs font-bold text-purple-700 group-hover:text-purple-800 flex items-center justify-between">
                    <span>Petugas</span>
                    <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">Officer</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">petugas@example.com</p>
            </a>
        </div>
    </div>

    <!-- Login Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@example.com"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                       placeholder="••••••••"
                       class="w-full text-sm rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-colors">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection
@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Login</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Login
        </button>
    </form>

    <p class="text-sm text-center mt-4">
        Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
    </p>

    <div class="mt-4 text-xs text-gray-500 bg-gray-50 p-3 rounded">
        <strong>Akun demo:</strong><br>
        admin@kampus.test / password<br>
        petugas@kampus.test / password<br>
        user@kampus.test / password
    </div>
</div>
@endsection
