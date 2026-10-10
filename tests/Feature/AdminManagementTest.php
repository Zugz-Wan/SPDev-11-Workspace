<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $petugas;

    private User $pengguna;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status_akun' => 'terverifikasi',
        ]);

        $this->petugas = User::factory()->create([
            'role' => 'petugas',
            'status_akun' => 'terverifikasi',
        ]);

        $this->pengguna = User::factory()->create([
            'role' => 'pengguna',
            'status_akun' => 'terverifikasi',
        ]);
    }

    public function test_admin_can_view_users_list_and_create_user_directly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Baru',
            'email' => 'petugas.baru@kampus.test',
            'password' => 'password123',
            'role' => 'petugas',
        ]);

        $createResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'petugas.baru@kampus.test',
            'role' => 'petugas',
            'status_akun' => 'terverifikasi',
        ]);
    }

    public function test_admin_can_verify_and_reject_pending_user(): void
    {
        $pendingUser = User::factory()->create([
            'role' => 'pengguna',
            'status_akun' => 'pending',
        ]);

        // Verify
        $verifyResponse = $this->actingAs($this->admin)->patch(route('admin.users.verify', $pendingUser->id));
        $verifyResponse->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'status_akun' => 'terverifikasi',
        ]);

        $pendingUser2 = User::factory()->create([
            'role' => 'pengguna',
            'status_akun' => 'pending',
        ]);

        // Reject
        $rejectResponse = $this->actingAs($this->admin)->delete(route('admin.users.reject', $pendingUser2->id));
        $rejectResponse->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'id' => $pendingUser2->id,
        ]);
    }

    public function test_pending_user_cannot_login_until_verified(): void
    {
        $pendingUser = User::factory()->create([
            'password' => bcrypt('secret123'),
            'role' => 'pengguna',
            'status_akun' => 'pending',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $pendingUser->email,
            'password' => 'secret123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_view_recap_and_export_csv(): void
    {
        $facility = Facility::create([
            'nama' => 'Aula Graha',
            'tipe' => 'aula',
            'kapasitas' => 500,
            'lokasi' => 'Gedung Pusat',
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recap.index'));
        $response->assertStatus(200);

        $exportResponse = $this->actingAs($this->admin)->get(route('admin.recap.export'));
        $exportResponse->assertStatus(200);
        $this->assertTrue(str_contains($exportResponse->headers->get('content-disposition'), 'attachment; filename="rekap_fasilitas_kampus_'));
    }
}
