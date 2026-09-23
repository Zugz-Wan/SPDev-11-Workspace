<!DOCTYPE html>
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