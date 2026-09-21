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
        // 1. Seed Demo Users
        $pengguna = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Ahmad Pengguna',
                'password' => Hash::make('password'),
                'role' => 'pengguna',
                'status_akun' => 'terverifikasi',
            ]
        );

        $petugas = User::updateOrCreate(
            ['email' => 'petugas@example.com'],
            [
                'name' => 'Budi Petugas Fasilitas',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status_akun' => 'terverifikasi',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status_akun' => 'terverifikasi',
            ]
        );

        // 2. Seed Facilities
        $f1 = Facility::updateOrCreate(
            ['nama' => 'Ruang Rapat VIP'],
            [
                'tipe' => 'ruangan',
                'kapasitas' => 20,
                'lokasi' => 'Gedung Rektorat Lt. 2',
                'status' => 'tersedia',
                'deskripsi' => 'Ruang rapat ber-AC dengan smart screen, sound system, dan meja konferensi oval.',
            ]
        );

        $f2 = Facility::updateOrCreate(
            ['nama' => 'Lab Komputer Multimedia'],
            [
                'tipe' => 'laboratorium',
                'kapasitas' => 45,
                'lokasi' => 'Gedung Fasilkom Lt. 3',
                'status' => 'tersedia',
                'deskripsi' => 'Laboratorium dengan 45 unit PC berspesifikasi tinggi untuk praktikum dan desain multimedia.',
            ]
        );

        $f3 = Facility::updateOrCreate(
            ['nama' => 'Lapangan Futsal Utama'],
            [
                'tipe' => 'lapangan',
                'kapasitas' => 14,
                'lokasi' => 'Area Olahraga Barat',
                'status' => 'perbaikan',
                'deskripsi' => 'Lapangan futsal rumput sintetis dengan pencahayaan LED malam hari dan jaring pengaman.',
            ]
        );

        $f4 = Facility::updateOrCreate(
            ['nama' => 'Aula Graha Utama'],
            [
                'tipe' => 'aula',
                'kapasitas' => 350,
                'lokasi' => 'Gedung Pusat Lt. 1',
                'status' => 'tersedia',
                'deskripsi' => 'Aula luas berkapasitas besar cocok untuk wisuda, seminar nasional, dan pameran.',
            ]
        );

        // 3. Seed Sample Reports (FR-REP-01 s/d FR-REP-05)
        Report::updateOrCreate(
            [
                'user_id' => $pengguna->id,
                'facility_id' => $f1->id,
                'deskripsi' => 'AC pendingin ruangan tidak berfungsi, suhu ruangan sangat panas saat rapat siang.',
            ],
            [
                'kategori' => 'Kelistrikan & Penerangan',
                'foto' => null,
                'status' => 'baru',
                'catatan' => null,
            ]
        );

        Report::updateOrCreate(
            [
                'user_id' => $pengguna->id,
                'facility_id' => $f3->id,
                'deskripsi' => 'Rumput sintetis di area gawang utara terkelupas dan membahayakan pemain.',
            ],
            [
                'kategori' => 'Kerusakan Fisik',
                'foto' => null,
                'status' => 'diproses',
                'catatan' => 'Petugas sedang melakukan pengeleman ulang dan perataan permukaan rumput sintetis.',
            ]
        );

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
}
