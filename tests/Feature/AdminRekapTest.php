<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRekapTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $pengguna;

    private User $petugas;

    private Facility $facilityA;

    private Facility $facilityB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['name' => 'Admin Utama', 'email' => 'admin@example.com']);
        $this->pengguna = User::factory()->pengguna()->create(['name' => 'Budi Pengguna', 'email' => 'budi@example.com']);
        $this->petugas = User::factory()->petugas()->create(['name' => 'Siti Petugas', 'email' => 'siti@example.com']);

        $this->facilityA = Facility::create([
            'nama' => 'Lab Komputer AI',
            'tipe' => 'laboratorium',
            'deskripsi' => 'Lab komputer cerdas',
            'kapasitas' => 40,
            'lokasi' => 'Gedung A Lantai 2',
            'status' => 'tersedia',
        ]);

        $this->facilityB = Facility::create([
            'nama' => 'Aula Utama',
            'tipe' => 'aula',
            'deskripsi' => 'Aula serbaguna',
            'kapasitas' => 300,
            'lokasi' => 'Gedung B Lantai 1',
            'status' => 'tersedia',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_rekap(): void
    {
        $response = $this->get(route('admin.rekap.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_rekap(): void
    {
        // Pengguna role
        $resPengguna = $this->actingAs($this->pengguna)->get(route('admin.rekap.index'));
        $resPengguna->assertStatus(403);

        // Petugas role
        $resPetugas = $this->actingAs($this->petugas)->get(route('admin.rekap.index'));
        $resPetugas->assertStatus(403);
    }

    public function test_admin_can_access_rekap_page_and_see_occupancy_and_damage_data(): void
    {
        // 1 approved reservation for facility A: 08:00 to 11:30 (3.5 hours = 210 mins)
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-10-15',
            'start_time' => '08:00',
            'end_time' => '11:30',
            'keperluan' => 'Workshop Machine Learning',
            'status' => 'disetujui',
        ]);

        // 1 pending reservation for facility A (must not count towards approved hours)
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-10-16',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'keperluan' => 'Kuliah Umum Pending',
            'status' => 'pending',
        ]);

        // 1 rejected reservation for facility A (must not count towards approved hours)
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-10-17',
            'start_time' => '13:00',
            'end_time' => '17:00',
            'keperluan' => 'Acara Ditolak',
            'status' => 'ditolak',
        ]);

        // 1 approved reservation for facility B: 13:00 to 15:00 (2 hours)
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityB->id,
            'tanggal' => '2026-10-18',
            'start_time' => '13:00',
            'end_time' => '15:00',
            'keperluan' => 'Seminar Nasional',
            'status' => 'disetujui',
        ]);

        // Damage reports
        Report::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'kategori' => 'AC Rusak',
            'deskripsi' => 'AC indoor bocor air',
            'status' => 'selesai',
        ]);

        Report::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'kategori' => 'Proyektor',
            'deskripsi' => 'Proyektor mati total',
            'status' => 'baru',
        ]);

        Report::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityB->id,
            'kategori' => 'Pintu Rusak',
            'deskripsi' => 'Engsel pintu aula patah',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.rekap.index'));

        $response->assertStatus(200);
        $response->assertSee('Rekap Okupansi & Kerusakan', false);
        $response->assertSee('1. Rekap Okupansi per Fasilitas');
        $response->assertSee('2. Frekuensi Kerusakan per Fasilitas');
        $response->assertSee('3. Frekuensi Kerusakan per Lokasi');

        // Check facility A stats
        $response->assertSee('Lab Komputer AI');
        $response->assertSee('3.5 Jam');
        $response->assertSee('Gedung A Lantai 2');

        // Check facility B stats
        $response->assertSee('Aula Utama');
        $response->assertSee('2 Jam');
        $response->assertSee('Gedung B Lantai 1');

        // Check damage reports summary
        $response->assertSee('3 Laporan');
    }

    public function test_rekap_filters_by_date_range(): void
    {
        // Reservation inside date range (June 2026)
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-06-10',
            'start_time' => '08:00',
            'end_time' => '12:00', // 4 hours
            'keperluan' => 'Pelatihan AI Juni',
            'status' => 'disetujui',
        ]);

        // Reservation outside date range (January 2026)
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-01-10',
            'start_time' => '08:00',
            'end_time' => '16:00', // 8 hours (outside)
            'keperluan' => 'Pelatihan AI Januari',
            'status' => 'disetujui',
        ]);

        // Report in June 2026
        $reportJune = Report::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'kategori' => 'Monitor',
            'deskripsi' => 'Monitor kedip-kedip',
            'status' => 'baru',
        ]);
        $reportJune->created_at = Carbon::parse('2026-06-15 10:00:00');
        $reportJune->save();

        // Report in January 2026 (outside)
        $reportJan = Report::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'kategori' => 'Kabel LAN',
            'deskripsi' => 'Kabel LAN putus',
            'status' => 'selesai',
        ]);
        $reportJan->created_at = Carbon::parse('2026-01-15 10:00:00');
        $reportJan->save();

        // Query June 2026
        $response = $this->actingAs($this->admin)->get(route('admin.rekap.index', [
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
        ]));

        $response->assertStatus(200);
        // Only 4 hours should be counted in June, not 12
        $response->assertSee('4 Jam');
        $response->assertDontSee('12 Jam');
    }

    public function test_admin_can_export_csv_for_all_and_specific_types(): void
    {
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-10-15',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Praktikum',
            'status' => 'disetujui',
        ]);

        Report::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'kategori' => 'AC',
            'deskripsi' => 'AC tidak dingin',
            'status' => 'baru',
        ]);

        // Export All
        $resAll = $this->actingAs($this->admin)->get(route('admin.rekap.export.csv', ['type' => 'all']));
        $resAll->assertStatus(200);
        $resAll->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Export Occupancy
        $resOccupancy = $this->actingAs($this->admin)->get(route('admin.rekap.export.csv', ['type' => 'occupancy']));
        $resOccupancy->assertStatus(200);

        // Export Damage Facility
        $resDamage = $this->actingAs($this->admin)->get(route('admin.rekap.export.csv', ['type' => 'damage_facility']));
        $resDamage->assertStatus(200);

        // Export Damage Location
        $resLocation = $this->actingAs($this->admin)->get(route('admin.rekap.export.csv', ['type' => 'damage_location']));
        $resLocation->assertStatus(200);
    }

    public function test_admin_can_access_print_friendly_page(): void
    {
        Reservation::create([
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facilityA->id,
            'tanggal' => '2026-10-15',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Praktikum',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.rekap.print'));

        $response->assertStatus(200);
        $response->assertSee('LAPORAN REKAPITULASI PENGGUNAAN & KERUSAKAN FASILITAS', false);
        $response->assertSee('Cetak / Simpan PDF');
        $response->assertSee('Lab Komputer AI');
    }
}
