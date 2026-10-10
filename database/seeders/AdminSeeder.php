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
        User::updateOrCreate(
            ['email' => 'admin@kampus.test'],
            [
                'name' => 'Admin Kampus',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status_akun' => 'terverifikasi',
            ]
        );

        // Akun Petugas
        User::updateOrCreate(
            ['email' => 'petugas@kampus.test'],
            [
                'name' => 'Petugas Fasilitas',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status_akun' => 'terverifikasi',
            ]
        );

        // Akun Pengguna
        User::updateOrCreate(
            ['email' => 'user@kampus.test'],
            [
                'name' => 'Pengguna Kampus',
                'password' => Hash::make('password'),
                'role' => 'pengguna',
                'status_akun' => 'terverifikasi',
            ]
        );
    }
}
