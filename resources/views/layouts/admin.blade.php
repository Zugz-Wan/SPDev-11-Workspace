<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Rekap - Sistem Reservasi Fasilitas')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold shadow-md shadow-indigo-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wider font-bold text-indigo-600">Sistem Reservasi & Pelaporan</span>
                        <h1 class="text-base font-extrabold text-slate-900 leading-none">Modul Admin Rekap</h1>
                    </div>
                </div>

                <nav class="flex items-center space-x-2">
                    <a href="{{ route('admin.rekap.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.rekap.index') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.rekap.okupansi') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.rekap.okupansi') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Rekap Okupansi
                    </a>
                    <a href="{{ route('admin.rekap.kerusakan') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.rekap.kerusakan') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Rekap Kerusakan
                    </a>
                    <div class="h-6 w-px bg-slate-200 mx-2"></div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Admin Mode
                    </span>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            Tugas Besar PPK &copy; 2026 - SPDev-11-Workspace. Modul Admin & Rekap (FR-ADM).
        </div>
    </footer>

</body>
</html>
