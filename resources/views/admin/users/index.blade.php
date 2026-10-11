@extends('layouts.app')

@section('title', 'Kelola Pengguna & Verifikasi Akun')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Manajemen Akun & Verifikasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola akun Petugas/Pengguna serta verifikasi permohonan registrasi akun mandiri.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Akun Langsung
            </a>
        </div>
    </div>

    <!-- Quick Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Menunggu Verifikasi</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⏳
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Filter Aktif</p>
                <p class="text-sm font-semibold text-slate-800 mt-1">
                    Role: {{ $roleFilter ?: 'Semua' }} | Status: {{ $statusFilter ?: 'Semua' }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                👥
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Akun Terdaftar</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $users->total() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                📋
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                   class="text-xs rounded-xl border border-slate-300 px-3.5 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

            <select name="role" class="text-xs rounded-xl border border-slate-300 px-3.5 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 bg-white">
                <option value="">-- Semua Role --</option>
                <option value="pengguna" {{ $roleFilter === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                <option value="petugas" {{ $roleFilter === 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <select name="status" class="text-xs rounded-xl border border-slate-300 px-3.5 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 bg-white">
                <option value="">-- Semua Status Akun --</option>
                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending (Menunggu Verifikasi)</option>
                <option value="terverifikasi" {{ $statusFilter === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
            </select>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-3.5">Pengguna</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5">Tgl Pendaftaran</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $user->status_akun === 'pending' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="text-slate-400 text-[11px]">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold
                                    @if($user->role === 'admin') bg-amber-100 text-amber-800
                                    @elseif($user->role === 'petugas') bg-purple-100 text-purple-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status_akun === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 animate-pulse">
                                        ⏳ Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        ✓ Terverifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $user->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($user->status_akun === 'pending')
                                    <form method="POST" action="{{ route('admin.users.verify', $user->id) }}" class="inline"
                                          onsubmit="return confirm('Verifikasi dan aktifkan akun {{ $user->name }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            ✓ Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" class="inline"
                                          onsubmit="return confirm('Tolak dan hapus akun permohonan {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition">
                                            ✕ Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data pengguna yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
