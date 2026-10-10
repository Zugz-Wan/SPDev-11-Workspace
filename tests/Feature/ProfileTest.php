<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@kampus.ac.id',
            'role' => 'pengguna',
            'status_akun' => 'terverifikasi',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('john@kampus.ac.id');
    }

    public function test_user_can_update_their_name(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@kampus.ac.id',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'New Name Updated',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name Updated',
            'email' => 'old@kampus.ac.id',
        ]);
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'old-password-123',
            'password' => 'new-secure-pass',
            'password_confirmation' => 'new-secure-pass',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('new-secure-pass', $user->password));
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrong-pass',
            'password' => 'new-secure-pass',
            'password_confirmation' => 'new-secure-pass',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('old-password-123', $user->password));
    }
}
