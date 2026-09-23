@extends('layouts.app')

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