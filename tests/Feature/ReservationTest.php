<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $petugas;
    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'pengguna',
            'status_akun' => 'terverifikasi',
        ]);

        $this->petugas = User::factory()->create([
            'role' => 'petugas',
            'status_akun' => 'terverifikasi',
        ]);

        $this->facility = Facility::create([
            'nama' => 'Lab Komputer 1',
            'tipe' => 'laboratorium',
            'deskripsi' => 'Fasilitas laboratorium komputer',
            'kapasitas' => 30,
            'lokasi' => 'Gedung A',
            'status' => 'tersedia',
        ]);
    }

    public function test_user_can_create_reservation_with_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/reservations', [
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'tujuan_penggunaan' => 'Praktikum Pemrograman',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'pending');

        $this->assertDatabaseHas('reservations', [
            'facility_id' => $this->facility->id,
            'user_id' => $this->user->id,
            'keperluan' => 'Praktikum Pemrograman',
        ]);
    }

    public function test_validation_fails_for_non_30_minute_interval(): void
    {
        $response = $this->actingAs($this->user)->postJson('/reservations', [
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:15',
            'end_time' => '10:00',
            'tujuan_penggunaan' => 'Seminar',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    }

    public function test_validation_fails_out_of_operating_hours(): void
    {
        $response = $this->actingAs($this->user)->postJson('/reservations', [
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '06:00',
            'end_time' => '07:30',
            'tujuan_penggunaan' => 'Klub Pagi',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    }

    public function test_validation_fails_when_schedule_conflicts_with_approved_reservation(): void
    {
        $tomorrow = now()->addDay()->format('Y-m-d');

        Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => $tomorrow,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Kuliah Umum',
            'status' => 'disetujui',
        ]);

        // Attempt overlapping reservation
        $response = $this->actingAs($this->user)->postJson('/reservations', [
            'facility_id' => $this->facility->id,
            'tanggal' => $tomorrow,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'tujuan_penggunaan' => 'Rapat Organisasi',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    }

    public function test_user_can_view_own_reservations(): void
    {
        Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Sesi Belajar',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->getJson('/my-reservations');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_user_can_cancel_own_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Sesi Belajar',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/reservations/{$reservation->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'dibatalkan',
        ]);
    }

    public function test_petugas_can_approve_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Sesi Belajar',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->putJson("/petugas/reservations/{$reservation->id}/approve");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'disetujui');
    }

    public function test_petugas_can_reject_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Sesi Belajar',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->putJson("/petugas/reservations/{$reservation->id}/reject", [
            'catatan_petugas' => 'Fasilitas sedang dalam perbaikan',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'ditolak')
            ->assertJsonPath('data.catatan_petugas', 'Fasilitas sedang dalam perbaikan');
    }

    public function test_petugas_can_cancel_reservation_with_reason(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Sesi Belajar',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->petugas)->putJson("/petugas/reservations/{$reservation->id}/cancel", [
            'alasan_pembatalan' => 'Gedung terpakai untuk acara rektorat',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'dibatalkan')
            ->assertJsonPath('data.catatan_petugas', 'Gedung terpakai untuk acara rektorat');
    }
}
