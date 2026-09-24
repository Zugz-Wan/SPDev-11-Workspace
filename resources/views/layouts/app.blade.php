<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sistem Manajemen Fasilitas & Pelaporan Kerusakan' }} - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Brand & Main Navigation -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('facilities.index') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-md shadow-indigo-100 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 tracking-tight text-base sm:text-lg block leading-tight">FasilitasKita</span>
                            <span class="text-xs text-slate-500 font-medium hidden sm:block">Pelaporan & Monitoring Fasilitas</span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex md:space-x-2">
                        <a href="{{ route('facilities.index') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('facilities.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Daftar Fasilitas
                        </a>

                        @auth
                            @if(auth()->user()->isPengguna())
                                <a href="{{ route('reports.create') }}"
                                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.create') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    + Lapor Kerusakan
                                </a>
                                <a href="{{ route('reports.index') }}"
                                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Status Laporan Saya
                                </a>
                            @endif

                            @if(auth()->user()->isPetugas())
                                <a href="{{ route('officer.reports.index') }}"
                                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('officer.reports.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Antrian Laporan
                                </a>
                                <a href="{{ route('reports.create') }}"
                                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.create') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    + Input Laporan
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Right: Profile / Auth Actions -->
                <div class="flex items-center space-x-3">
                    @auth
                        <div class="flex items-center space-x-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-slate-800 leading-none">{{ auth()->user()->name }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ auth()->user()->role === 'petugas' ? 'bg-purple-100 text-purple-700' : (auth()->user()->role === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </div>

                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Logout">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                                Masuk
                            </a>
                            <div class="relative inline-block group">
                                <button type="button" class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200 flex items-center space-x-1">
                                    <span>Demo Login</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 hidden group-hover:block z-50">
                                    <a href="{{ route('quick-login', 'pengguna') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        Login sbg <strong>Pengguna</strong>
                                    </a>
                                    <a href="{{ route('quick-login', 'petugas') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-purple-50 hover:text-purple-700">
                                        Login sbg <strong>Petugas</strong>
                                    </a>
                                    <a href="{{ route('quick-login', 'admin') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-700">
                                        Login sbg <strong>Admin</strong>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Submenu -->
        <div class="md:hidden border-t border-slate-100 px-4 py-2 flex space-x-2 overflow-x-auto bg-slate-50/70">
            <a href="{{ route('facilities.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap {{ request()->routeIs('facilities.*') ? 'bg-indigo-600 text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Fasilitas</a>
            @auth
                @if(auth()->user()->isPengguna())
                    <a href="{{ route('reports.create') }}" class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap {{ request()->routeIs('reports.create') ? 'bg-indigo-600 text-white' : 'text-slate-600 bg-white border border-slate-200' }}">+ Lapor Kerusakan</a>
                    <a href="{{ route('reports.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap {{ request()->routeIs('reports.index') ? 'bg-indigo-600 text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Laporan Saya</a>
                @endif
                @if(auth()->user()->isPetugas())
                    <a href="{{ route('officer.reports.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap {{ request()->routeIs('officer.reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Antrian Petugas</a>
                @endif
            @endauth
        </div>
    </nav>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm" role="alert">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-sm" role="alert">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-sm" role="alert">
                <div class="flex items-center mb-1">
                    <svg class="w-5 h-5 flex-shrink-0 mr-2 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm font-semibold">Terdapat beberapa kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 ml-6 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
            <p>&copy; {{ date('Y') }} FasilitasKita - Sistem Pelaporan Kerusakan Fasilitas (FR-REP-01 s/d FR-REP-05).</p>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                    Sistem Operasional
                </span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reservasi Fasilitas Kampus')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/facilities" class="text-xl font-bold text-blue-600">
                🏛️ Reservasi Fasilitas
            </a>
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm text-gray-600">
                        Halo, <strong>{{ auth()->user()->name }}</strong>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                            {{ auth()->user()->role }}
                        </span>
                    </span>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.facilities.index') }}" class="text-sm text-gray-700 hover:text-blue-600">
                            Kelola Fasilitas
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Content --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="text-center text-sm text-gray-500 py-6">
        &copy; {{ date('Y') }} PPK 2026 — Sistem Reservasi & Pelaporan Fasilitas Kampus
    </footer>

</body>
</html>
