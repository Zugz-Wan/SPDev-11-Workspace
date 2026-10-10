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

    public function test_facility_show_displays_slot_availability_and_omits_legacy_notice(): void
    {
        $response = $this->get("/facilities/{$this->facility->id}");

        $response->assertStatus(200)
            ->assertSee('Ketersediaan Slot')
            ->assertSee('07:00 - 07:30')
            ->assertSee('Tersedia')
            ->assertDontSee('Fitur reservasi online akan hadir di modul selanjutnya');
    }

    public function test_user_can_submit_reservation_via_web_form_and_redirects_with_flash_message(): void
    {
        $response = $this->actingAs($this->user)->post('/reservations', [
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Rapat Kegiatan Himpunan',
        ]);

        $response->assertRedirect(route('reservations.my'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'facility_id' => $this->facility->id,
            'user_id' => $this->user->id,
            'keperluan' => 'Rapat Kegiatan Himpunan',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_view_my_reservations_page_via_named_route(): void
    {
        Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Diskusi Proyek',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('reservations.my'));

        $response->assertStatus(200)
            ->assertSee('Reservasi Saya')
            ->assertSee('Diskusi Proyek')
            ->assertSee('Batalkan');
    }

    public function test_user_can_view_reservation_show_page(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Diskusi Proyek',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('reservations.show', $reservation->id));

        $response->assertStatus(200)
            ->assertSee('Detail Informasi Reservasi')
            ->assertSee('Diskusi Proyek');
    }

    public function test_user_can_cancel_reservation_via_web_delete(): void
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

        $response = $this->actingAs($this->user)->delete(route('reservations.destroy', $reservation->id));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'dibatalkan',
        ]);
    }

    public function test_petugas_can_view_petugas_reservations_index_and_counts(): void
    {
        Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Praktikum Komputer',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.reservations.index'));

        $response->assertStatus(200)
            ->assertSee('Antrian Reservasi Fasilitas')
            ->assertSee('Reservasi Pending')
            ->assertSee('Praktikum Komputer')
            ->assertSee('Setujui')
            ->assertSee('Tolak');
    }

    public function test_petugas_can_approve_reservation_via_web(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Praktikum Komputer',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->put(route('petugas.reservations.approve', $reservation->id));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals('disetujui', $reservation->fresh()->status);
    }

    public function test_petugas_can_reject_reservation_via_web(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Praktikum Komputer',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->put(route('petugas.reservations.reject', $reservation->id), [
            'catatan_petugas' => 'Fasilitas sedang diperbaiki',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $reservation->fresh();
        $this->assertEquals('ditolak', $fresh->status);
        $this->assertEquals('Fasilitas sedang diperbaiki', $fresh->catatan_petugas);
    }

    public function test_petugas_can_cancel_approved_reservation_via_web_with_reason(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Praktikum Komputer',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->petugas)->put(route('petugas.reservations.cancel', $reservation->id), [
            'alasan_pembatalan' => 'Acara dinas mendadak',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $reservation->fresh();
        $this->assertEquals('dibatalkan', $fresh->status);
        $this->assertEquals('Acara dinas mendadak', $fresh->catatan_petugas);
    }

    public function test_approve_conflicting_reservation_is_rejected(): void
    {
        $tomorrow = now()->addDay()->format('Y-m-d');

        // Existing approved reservation 08:00 - 10:00
        Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => $tomorrow,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'keperluan' => 'Kuliah Umum',
            'status' => 'disetujui',
        ]);

        // Pending reservation overlapping 09:00 - 11:00
        $pending = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => $tomorrow,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'keperluan' => 'Rapat Organisasi',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->putJson(route('petugas.reservations.approve', $pending->id));

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Jadwal reservasi bentrok dengan reservasi lain yang sudah disetujui.');

        $this->assertEquals('pending', $pending->fresh()->status);
    }

    public function test_approve_non_pending_reservation_is_rejected(): void
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

        $response = $this->actingAs($this->petugas)->putJson(route('petugas.reservations.approve', $reservation->id));

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Hanya reservasi pending yang bisa disetujui');
    }

    public function test_cancel_without_reason_is_rejected(): void
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

        $response = $this->actingAs($this->petugas)->putJson(route('petugas.reservations.cancel', $reservation->id), [
            'alasan_pembatalan' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['alasan_pembatalan']);

        $this->assertEquals('disetujui', $reservation->fresh()->status);
    }

    public function test_user_cancellation_past_deadline_is_rejected(): void
    {
        $this->travelTo(now()->setTime(10, 0, 0));

        // Reservation starting at 11:00 (only 1 hour away, less than 2 hours)
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'tanggal' => now()->format('Y-m-d'),
            'start_time' => '11:00',
            'end_time' => '12:00',
            'keperluan' => 'Sesi Belajar',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('reservations.destroy', $reservation->id));

        $response->assertStatus(422);

        $this->assertEquals('pending', $reservation->fresh()->status);
    }

    public function test_store_fails_when_today_start_time_has_passed(): void
    {
        $this->travelTo(now()->setTime(10, 0, 0));

        $response = $this->actingAs($this->user)->postJson('/reservations', [
            'facility_id' => $this->facility->id,
            'tanggal' => now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'keperluan' => 'Sesi Belajar',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    }
}
