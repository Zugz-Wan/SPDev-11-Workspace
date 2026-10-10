<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Core Accounts (@kampus.test)
        $pengguna = User::updateOrCreate(
            ['email' => 'user@kampus.test'],
            [
                'name' => 'Pengguna Biasa',
                'password' => Hash::make('password'),
                'role' => 'pengguna',
                'status_akun' => 'terverifikasi',
            ]
        );

        $petugas = User::updateOrCreate(
            ['email' => 'petugas@kampus.test'],
            [
                'name' => 'Petugas Fasilitas',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status_akun' => 'terverifikasi',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@kampus.test'],
            [
                'name' => 'Admin Kampus',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status_akun' => 'terverifikasi',
            ]
        );

        // 2. Seed Facilities
        $this->call(FacilitySeeder::class);

        // 3. Seed Sample Reports
        $f1 = Facility::where('nama', 'Ruang Rapat Dosen')->first() ?? Facility::first();
        $f2 = Facility::where('nama', 'Laboratorium Komputer 1')->first() ?? Facility::first();
        $f3 = Facility::where('nama', 'Aula Serbaguna')->first() ?? Facility::first();

        if ($f1) {
            Report::updateOrCreate(
                [
                    'user_id' => $pengguna->id,
                    'facility_id' => $f1->id,
                    'deskripsi' => 'AC pendingin ruangan tidak berfungsi, suhu ruangan sangat panas saat rapat siang.',
                ],
                [
                    'kategori' => 'Kelistrikan & Penerangan',
                    'foto' => null,
                    'status' => 'diproses',
                    'catatan' => 'Petugas teknisi sedang melakukan pemeriksaan unit outdoor AC.',
                ]
            );
        }

        if ($f2) {
            Report::updateOrCreate(
                [
                    'user_id' => $pengguna->id,
                    'facility_id' => $f2->id,
                    'deskripsi' => 'Proyektor utama berkedip-kedip lalu mati total saat kegiatan praktikum berlangsung.',
                ],
                [
                    'kategori' => 'Perangkat / Komputer',
                    'foto' => null,
                    'status' => 'selesai',
                    'catatan' => 'Kabel HDMI dan adaptor daya telah diganti baru. Proyektor berfungsi normal kembali.',
                ]
            );
        }

        if ($f3) {
            Report::updateOrCreate(
                [
                    'user_id' => $pengguna->id,
                    'facility_id' => $f3->id,
                    'deskripsi' => 'Lampu sorot panggung bagian barat mati 2 buah.',
                ],
                [
                    'kategori' => 'Kelistrikan & Penerangan',
                    'foto' => null,
                    'status' => 'baru',
                    'catatan' => null,
                ]
            );
        }
    }
}
