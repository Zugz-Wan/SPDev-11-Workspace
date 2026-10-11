# SIREFA (Sistem Reservasi dan Report Fasilitas Kampus)

SIREFA adalah sistem informasi berbasis web terpadu untuk pengelolaan fasilitas kampus yang mencakup katalog ketersediaan ruangan real-time, pengajuan serta persetujuan reservasi fasilitas, pelaporan dan pemantauan kerusakan fasilitas, hingga rekapitulasi okupansi dan riwayat kerusakan resmi.

---

## 👥 4 Aktor Sistem

1. **Pengunjung (Guest)**:
   - Menjelajahi katalog fasilitas kampus lengkap dengan filter (tipe, lokasi, kapasitas, status).
   - Melihat ketersediaan ruangan dan jadwal pemakaian.
   - Melakukan pendaftaran akun mandiri untuk diverifikasi oleh administrator.

2. **Pengguna (Civitas Akademika / Mahasiswa / Dosen)**:
   - Mengajukan reservasi fasilitas kampus secara online.
   - Membatalkan reservasi mandiri dengan batasan minimal waktu pembatalan.
   - Membuat laporan kerusakan fasilitas disertai foto bukti kerusakan.
   - Memantau riwayat status reservasi dan status aduan kerusakan.
   - Mengelola profil akun pribadi (nama dan kata sandi).

3. **Petugas Fasilitas**:
   - Memverifikasi antrian permohonan reservasi (setujui / tolak / batalkan) dengan validasi pencegahan bentrok jadwal otomatis.
   - Mengelola antrian laporan kerusakan fasilitas dan memperbarui status penanganannya (*baru* &rarr; *diproses* &rarr; *selesai* / *ditolak*).
   - Mengubah status fasilitas menjadi *perbaikan* secara otomatis saat ada penanganan aduan.

4. **Administrator**:
   - Memiliki seluruh kewenangan Petugas Fasilitas.
   - Mengelola master data fasilitas kampus (tambah, edit, hapus ruangan).
   - Memverifikasi atau menolak registrasi akun pengguna baru, serta mendaftarkan akun staf/petugas langsung.
   - Mengakses dashboard rekapitulasi okupansi ruangan dan frekuensi kerusakan, cetak laporan *print-friendly* (PDF), dan ekspor data ke format CSV.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 13 (PHP 8.4+)
- **Database**: MySQL
- **Frontend**: Blade Templating, Tailwind CSS, Vite
- **Deployment Platform**: Railway

---

## 🚀 Langkah Menjalankan Proyek Secara Lokal

Pastikan Anda telah menginstal **PHP 8.4+**, **Composer**, **Node.js & NPM**, serta memiliki layanan **MySQL** yang aktif (misalnya via Laragon/XAMPP).

```bash
# 1. Install dependensi PHP & Node.js
composer install
npm install

# 2. Salin environment configuration & buat application key
copy .env.example .env
php artisan key:generate

# 3. Sesuaikan konfigurasi database di file .env, lalu jalankan migrasi & seeder
php artisan migrate:fresh --seed

# 4. Buat symbolic link untuk penyimpanan berkas media/gambar
php artisan storage:link

# 5. Kompilasi aset frontend
npm run build

# 6. Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## 🔑 Akun Demo Bawaan

| Role | Email | Password |
|---|---|---|
| **Pengguna** | `user@kampus.test` | `password` |
| **Petugas** | `petugas@kampus.test` | `password` |
| **Administrator** | `admin@kampus.test` | `password` |

---

## 🌐 Link Deployment

Aplikasi SIREFA telah dideploy secara publik di:  
🔗 **[https://sirefa-project.up.railway.app](https://sirefa-project.up.railway.app)**
