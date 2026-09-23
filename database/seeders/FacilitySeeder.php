<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'nama' => 'Aula Serbaguna',
                'tipe' => 'aula',
                'kapasitas' => 500,
                'lokasi' => 'Gedung A Lantai 1',
                'status' => 'tersedia',
                'deskripsi' => 'Aula besar untuk acara kampus, seminar, dan wisuda.',
            ],
            [
                'nama' => 'Laboratorium Komputer 1',
                'tipe' => 'laboratorium',
                'kapasitas' => 40,
                'lokasi' => 'Gedung B Lantai 2',
                'status' => 'tersedia',
                'deskripsi' => 'Lab komputer dengan 40 PC dan proyektor.',
            ],
            [
                'nama' => 'Ruang Kelas 101',
                'tipe' => 'ruangan',
                'kapasitas' => 30,
                'lokasi' => 'Gedung C Lantai 1',
                'status' => 'tersedia',
                'deskripsi' => 'Ruang kelas standar dengan AC dan whiteboard.',
            ],
            [
                'nama' => 'Lapangan Basket',
                'tipe' => 'lapangan',
                'kapasitas' => 100,
                'lokasi' => 'Area Outdoor Belakang',
                'status' => 'tersedia',
                'deskripsi' => 'Lapangan basket outdoor dengan lampu.',
            ],
            [
                'nama' => 'Ruang Rapat Dosen',
                'tipe' => 'ruangan',
                'kapasitas' => 20,
                'lokasi' => 'Gedung A Lantai 3',
                'status' => 'perbaikan',
                'deskripsi' => 'Ruang rapat sedang renovasi AC.',
            ],
            [
                'nama' => 'Laboratorium Fisika',
                'tipe' => 'laboratorium',
                'kapasitas' => 25,
                'lokasi' => 'Gedung B Lantai 1',
                'status' => 'tersedia',
                'deskripsi' => 'Lab fisika dengan peralatan praktikum lengkap.',
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}