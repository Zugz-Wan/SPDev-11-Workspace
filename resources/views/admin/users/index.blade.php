@extends('layouts.app')

@section('title', 'Manajemen Akun Pengguna')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1 rounded-full text-xs font-semibold mb-2">
                <span>Admin Panel • Manajemen Akun</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Kelola Akun Pengguna</h1>
            <p class="text-slate-500 text-sm mt-1">Verifikasi pendaftar baru, buat akun manual, dan pantau seluruh akun di sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Akun Baru
        </a>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <a href="{{ route('admin.users.index') }}"
           class="p-4 rounded-2xl border transition-all {{ $statusFilter === '' && $roleFilter === '' ? 'bg-amber-50 border-amber-200 ring-2 ring-amber-400/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold text-slate-500">Total Akun</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['total'] }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['status_akun' => 'pending']) }}"
           class="p-4 rounded-2xl border transition-all {{ $statusFilter === 'pending' ? 'bg-amber-50 border-amber-200 ring-2 ring-amber-400/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-amber-700">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Menunggu Verifikasi</span>
            </div>
            <div class="text-2xl font-bold text-amber-900 mt-1">{{ $counts['pending'] }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['status_akun' => 'terverifikasi']) }}"
           class="p-4 rounded-2xl border transition-all {{ $statusFilter === 'terverifikasi' ? 'bg-emerald-50 border-emerald-200 ring-2 ring-emerald-400/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Terverifikasi</span>
            </div>
            <div class="text-2xl font-bold text-emerald-900 mt-1">{{ $counts['terverifikasi'] }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['status_akun' => 'ditolak']) }}"
           class="p-4 rounded-2xl border transition-all {{ $statusFilter === 'ditolak' ? 'bg-rose-50 border-rose-200 ring-2 ring-rose-400/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div class="flex items-center space-x-1.5 text-xs font-semibold text-rose-700">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Ditolak</span>
            </div>
            <div class="text-2xl font-bold text-rose-900 mt-1">{{ $counts['ditolak'] }}</div>
        </a>
    </div>

    {{-- Bagian Khusus: Menunggu Verifikasi --}}
    @if($pendingUsers->isNotEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-amber-900">Pendaftaran Menunggu Verifikasi ({{ $pendingUsers->count() }})</h2>
                    <p class="text-xs text-amber-700/80">Akun berikut mendaftar mandiri dan memerlukan persetujuan admin.</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($pendingUsers as $pending)
                    <div class="bg-white rounded-2xl border border-amber-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="font-semibold text-slate-900 text-sm">{{ $pending->name }}</div>
                            <div class="text-xs text-slate-500">{{ $pending->email }}</div>
                            <div class="text-[11px] text-slate-400">Daftar {{ $pending->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form action="{{ route('admin.users.verify', $pending) }}" method="POST"
                                  onsubmit="return confirm('Verifikasi akun {{ addslashes($pending->name) }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                    ✓ Verifikasi
                                </button>
                            </form>
                            <form action="{{ route('admin.users.reject', $pending) }}" method="POST"
                                  onsubmit="return confirm('Tolak akun {{ addslashes($pending->name) }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                                    ✕ Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama atau email..."
                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <select name="role"
                        class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Semua Role</option>
                    <option value="pengguna" {{ $roleFilter === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    <option value="petugas" {{ $roleFilter === 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div>
                <select name="status_akun"
                        class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="terverifikasi" {{ $statusFilter === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="ditolak" {{ $statusFilter === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 transition-colors">
                    Terapkan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-3 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    @if($users->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-3">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Tidak Ada Akun Ditemukan</h3>
            <p class="text-slate-500 text-sm">Tidak ada akun yang cocok dengan filter yang dipilih.</p>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-600 font-semibold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                        <tr>
                            <th class="py-3.5 px-6">Nama & Email</th>
                            <th class="py-3.5 px-4">Role</th>
                            <th class="py-3.5 px-4 text-center">Status Akun</th>
                            <th class="py-3.5 px-4">Terdaftar</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-slate-900 text-sm">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</div>
                                    @if($user->id === auth()->id())
                                        <span class="text-[10px] text-amber-600 font-semibold">(Akun Anda)</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                        {{ $user->role === 'admin' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                                           ($user->role === 'petugas' ? 'bg-purple-50 text-purple-700 border border-purple-200' :
                                                                        'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                        {{ $user->status_akun === 'terverifikasi' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                           ($user->status_akun === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                                                                               'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                        @if($user->status_akun === 'pending')
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        @endif
                                        {{ $user->status_akun === 'terverifikasi' ? 'Terverifikasi' :
                                           ($user->status_akun === 'pending' ? 'Menunggu' : 'Ditolak') }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-500 whitespace-nowrap">
                                    {{ $user->created_at->translatedFormat('d M Y') }}
                                    <span class="block text-[10px] text-slate-400">{{ $user->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($user->status_akun === 'pending' && $user->role === 'pengguna')
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.users.verify', $user) }}" method="POST"
                                                  onsubmit="return confirm('Verifikasi akun ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                                    Verifikasi
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.reject', $user) }}" method="POST"
                                                  onsubmit="return confirm('Tolak akun ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
