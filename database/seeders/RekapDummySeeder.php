<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RekapDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status_akun' => 'terverifikasi',
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@mail.com'],
            [
                'name' => 'Petugas Fasilitas',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'status_akun' => 'terverifikasi',
            ]
        );

        $mhs1 = User::firstOrCreate(
            ['email' => 'budi@mail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'pengguna',
                'status_akun' => 'terverifikasi',
            ]
        );

        $mhs2 = User::firstOrCreate(
            ['email' => 'siti@mail.com'],
            [
                'name' => 'Siti Rahma',
                'password' => Hash::make('password123'),
                'role' => 'pengguna',
                'status_akun' => 'terverifikasi',
            ]
        );

        $mhs3 = User::firstOrCreate(
            ['email' => 'andi@mail.com'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password123'),
                'role' => 'pengguna',
                'status_akun' => 'terverifikasi',
            ]
        );

        // 2. Facilities
        $facilitiesData = [
            [
                'nama' => 'Ruang Seminar Utama',
                'tipe' => 'ruangan',
                'kapasitas' => 80,
                'lokasi' => 'Gedung A Lt. 3',
                'status' => 'tersedia',
                'deskripsi' => 'Dilengkapi proyektor ganda, sound system, dan AC sentral',
            ],
            [
                'nama' => 'Lab Komputer Rekayasa Web',
                'tipe' => 'laboratorium',
                'kapasitas' => 40,
                'lokasi' => 'Gedung B Lt. 2',
                'status' => 'tersedia',
                'deskripsi' => '40 Unit PC Core i7 dengan koneksi internet cepat',
            ],
            [
                'nama' => 'Lab Jaringan Komputer',
                'tipe' => 'laboratorium',
                'kapasitas' => 35,
                'lokasi' => 'Gedung B Lt. 3',
                'status' => 'tersedia',
                'deskripsi' => 'Perangkat router cisco, switch managed, dan rack server',
            ],
            [
                'nama' => 'Aula Serbaguna Ganesha',
                'tipe' => 'aula',
                'kapasitas' => 300,
                'lokasi' => 'Gedung Rektorat Lt. 1',
                'status' => 'tersedia',
                'deskripsi' => 'Cocok untuk wisuda, pelantikan, pameran seni, dan gathering akbar',
            ],
            [
                'nama' => 'Lapangan Futsal Indoor',
                'tipe' => 'lapangan',
                'kapasitas' => 50,
                'lokasi' => 'Pusat Olahraga Lt. 1',
                'status' => 'tersedia',
                'deskripsi' => 'Lantai vinyl standar kompetisi dengan penerangan malam hari',
            ],
            [
                'nama' => 'Lapangan Basket Outdoor',
                'tipe' => 'lapangan',
                'kapasitas' => 60,
                'lokasi' => 'Pusat Olahraga Area Terbuka',
                'status' => 'tersedia',
                'deskripsi' => 'Lapangan aspal cor cat akrilik dengan tribun penonton',
            ],
            [
                'nama' => 'Ruang Rapat Dekanat',
                'tipe' => 'ruangan',
                'kapasitas' => 25,
                'lokasi' => 'Gedung A Lt. 2',
                'status' => 'tersedia',
                'deskripsi' => 'Meja oval konseptual dengan mikrofon meja per kursi',
            ],
            [
                'nama' => 'Ruang Teater / Mini Studio',
                'tipe' => 'ruangan',
                'kapasitas' => 50,
                'lokasi' => 'Gedung C Lt. 1',
                'status' => 'perbaikan',
                'deskripsi' => 'Ruang kedap suara dengan sistem audio surround dolby',
            ],
        ];

        $facilities = [];
        foreach ($facilitiesData as $f) {
            $facilities[] = Facility::firstOrCreate(['nama' => $f['nama']], $f);
        }

        // 3. Reservations (across multiple dates)
        $users = [$mhs1, $mhs2, $mhs3];
        $statuses = ['disetujui', 'disetujui', 'disetujui', 'pending', 'ditolak'];
        $purposes = [
            'Kuliah Tamu Industri Software',
            'Praktikum Pemrograman Web Lanjut',
            'Latihan Turnamen Futsal Antar Angkatan',
            'Seminar Nasional Teknologi Informasi',
            'Rapat Koordinasi BEM & Himpunan',
            'Pelatihan Cloud Computing & DevOps',
            'Latihan Pertandingan Basket Ekshibisi',
            'Workshop UI/UX Design & Prototyping',
            'Ujian Sertifikasi Kompetensi Jaringan',
            'Gladi Bersih Pentas Seni Mahasiswa',
        ];

        // Buat 35 data reservasi tersebar dalam rentang waktu
        for ($i = 0; $i < 35; $i++) {
            $targetFacility = $facilities[$i % count($facilities)];
            $targetUser = $users[$i % count($users)];
            $daysOffset = ($i * 2) - 45; // Dari 45 hari lalu sampai 25 hari ke depan
            $date = Carbon::today()->addDays($daysOffset)->format('Y-m-d');
            
            $startHour = 8 + (($i % 4) * 3); // 08:00, 11:00, 14:00, 17:00
            $duration = ($i % 3 == 0) ? 3 : 2; // 2 atau 3 jam
            $startTime = sprintf('%02d:00:00', $startHour);
            $endTime = sprintf('%02d:00:00', $startHour + $duration);

            Reservation::firstOrCreate(
                [
                    'facility_id' => $targetFacility->id,
                    'tanggal' => $date,
                    'start_time' => $startTime,
                ],
                [
                    'user_id' => $targetUser->id,
                    'end_time' => $endTime,
                    'keperluan' => $purposes[$i % count($purposes)],
                    'status' => $statuses[$i % count($statuses)],
                    'catatan_petugas' => ($statuses[$i % count($statuses)] === 'ditolak') ? 'Jadwal bentrok dengan kegiatan fakultas' : 'Disetujui sesuai SOP',
                ]
            );
        }

        // 4. Reports (Damage frequency per facility/location)
        $damageReports = [
            [
                'facility_index' => 1, // Lab Komputer Rekayasa Web
                'deskripsi' => '2 Unit PC tidak mau menyala setelah lonjakan listrik.',
                'status' => 'selesai',
            ],
            [
                'facility_index' => 1, // Lab Komputer Rekayasa Web
                'deskripsi' => 'Kabel LAN pada meja baris ke-3 putus/kendor.',
                'status' => 'diproses',
            ],
            [
                'facility_index' => 0, // Ruang Seminar Utama
                'deskripsi' => 'Remote AC hilang dan remote proyektor proyektor redup.',
                'status' => 'selesai',
            ],
            [
                'facility_index' => 0, // Ruang Seminar Utama
                'deskripsi' => 'Sound system mengeluarkan suara dengung konstan.',
                'status' => 'diproses',
            ],
            [
                'facility_index' => 2, // Lab Jaringan Komputer
                'deskripsi' => 'Port switch nomor 12 sampai 16 tidak menyalurkan sinyal PoE.',
                'status' => 'menunggu',
            ],
            [
                'facility_index' => 3, // Aula Serbaguna Ganesha
                'deskripsi' => 'Lampu sorot panggung bagian kanan padam.',
                'status' => 'selesai',
            ],
            [
                'facility_index' => 3, // Aula Serbaguna Ganesha
                'deskripsi' => 'Engsel pintu darurat sebelah barat macet dan berderit.',
                'status' => 'menunggu',
            ],
            [
                'facility_index' => 4, // Lapangan Futsal Indoor
                'deskripsi' => 'Jaring gawang sisi utara robek cukup besar.',
                'status' => 'selesai',
            ],
            [
                'facility_index' => 4, // Lapangan Futsal Indoor
                'deskripsi' => 'Lantai vinyl di dekat garis tengah mulai menggelembung.',
                'status' => 'diproses',
            ],
            [
                'facility_index' => 5, // Lapangan Basket Outdoor
                'deskripsi' => 'Ring basket sisi selatan agak miring setelah pertandingan.',
                'status' => 'menunggu',
            ],
            [
                'facility_index' => 7, // Ruang Teater
                'deskripsi' => 'Sistem tata suara tidak merata, speaker belakang mati total.',
                'status' => 'diproses',
            ],
            [
                'facility_index' => 7, // Ruang Teater
                'deskripsi' => 'AC bocor dan menetes ke karpet lantai.',
                'status' => 'menunggu',
            ],
        ];

        foreach ($damageReports as $dr) {
            $fac = $facilities[$dr['facility_index']];
            Report::firstOrCreate(
                [
                    'facility_id' => $fac->id,
                    'deskripsi' => $dr['deskripsi'],
                ],
                [
                    'user_id' => $mhs1->id,
                    'status' => $dr['status'],
                    'foto' => null,
                ]
            );
        }
    }
}
