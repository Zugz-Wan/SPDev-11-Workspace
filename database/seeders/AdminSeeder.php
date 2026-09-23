<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Admin Kampus',
            'email' => 'admin@kampus.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_akun' => 'terverifikasi',
        ]);

        // Akun Petugas
        User::create([
            'name' => 'Petugas Fasilitas',
            'email' => 'petugas@kampus.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'status_akun' => 'terverifikasi',
        ]);

        // Akun Pengguna (buat testing)
        User::create([
            'name' => 'Pengguna Biasa',
            'email' => 'user@kampus.test',
            'password' => Hash::make('password'),
            'role' => 'pengguna',
            'status_akun' => 'terverifikasi',
        ]);
    }
}