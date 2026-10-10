<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_facilities_can_be_filtered_by_minimum_capacity(): void
    {
        $smallFacility = Facility::create([
            'nama' => 'Ruang Rapat Kecil',
            'tipe' => 'ruangan',
            'kapasitas' => 10,
            'lokasi' => 'Gedung A Lt 1',
            'status' => 'tersedia',
        ]);

        $largeFacility = Facility::create([
            'nama' => 'Aula Besar Kampus',
            'tipe' => 'aula',
            'kapasitas' => 200,
            'lokasi' => 'Gedung Pusat Lt 1',
            'status' => 'tersedia',
        ]);

        // Filter kapasitas >= 50
        $response = $this->get(route('facilities.index', ['kapasitas' => 50]));

        $response->assertStatus(200);
        $response->assertSee('Aula Besar Kampus');
        $response->assertDontSee('Ruang Rapat Kecil');
    }

    public function test_admin_destroy_deactivates_facility_without_deleting(): void
    {
        $admin = User::factory()->admin()->create();
        $facility = Facility::create([
            'nama' => 'Lab Hardware',
            'tipe' => 'laboratorium',
            'kapasitas' => 30,
            'lokasi' => 'Gedung B Lt 2',
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.facilities.destroy', $facility->id));

        $response->assertRedirect(route('admin.facilities.index'));
        $response->assertSessionHas('success', 'Fasilitas dinonaktifkan.');

        // Record must still exist in the database with status tidak_aktif
        $this->assertDatabaseHas('facilities', [
            'id' => $facility->id,
            'status' => 'tidak_aktif',
        ]);
    }
}
