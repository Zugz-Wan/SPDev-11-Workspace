<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_does_not_autologin_and_redirects_to_login_with_message(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Registrasi berhasil, akun menunggu verifikasi admin.');

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'status_akun' => 'pending',
            'role' => 'pengguna',
        ]);
    }

    public function test_login_rejects_pending_user(): void
    {
        $user = User::factory()->create([
            'email' => 'pending@example.com',
            'password' => Hash::make('password123'),
            'status_akun' => 'pending',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Akun Anda belum diverifikasi admin',
        ]);
    }

    public function test_login_rejects_ditolak_user(): void
    {
        $user = User::factory()->create([
            'email' => 'ditolak@example.com',
            'password' => Hash::make('password123'),
            'status_akun' => 'ditolak',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'ditolak@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Registrasi akun Anda ditolak',
        ]);
    }

    public function test_login_allows_terverifikasi_user(): void
    {
        $user = User::factory()->create([
            'email' => 'aktif@example.com',
            'password' => Hash::make('password123'),
            'status_akun' => 'terverifikasi',
            'role' => 'pengguna',
        ]);

        $response = $this->post('/login', [
            'email' => 'aktif@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('reports.index'));
    }

    public function test_quick_login_route_is_removed(): void
    {
        $response = $this->get('/quick-login/admin');

        $response->assertNotFound();
    }

    public function test_role_middleware_allows_authorized_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_akun' => 'terverifikasi',
        ]);

        $response = $this->actingAs($admin)->get('/admin/facilities');

        $response->assertOk();
    }

    public function test_role_middleware_aborts_unauthorized_role(): void
    {
        $pengguna = User::factory()->create([
            'role' => 'pengguna',
            'status_akun' => 'terverifikasi',
        ]);

        $response = $this->actingAs($pengguna)->get('/admin/facilities');

        $response->assertForbidden();
    }
}
