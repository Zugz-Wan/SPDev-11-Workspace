<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_report_create_page(): void
    {
        $response = $this->get(route('reports.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_report_create_page(): void
    {
        $user = User::factory()->pengguna()->create();
        $facility = Facility::factory()->create(['nama' => 'Lab Fisika']);

        $response = $this->actingAs($user)->get(route('reports.create'));

        $response->assertStatus(200);
        $response->assertSee('Laporkan Kerusakan Fasilitas');
        $response->assertSee('Lab Fisika');
    }

    public function test_user_can_submit_damage_report_with_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->pengguna()->create();
        $facility = Facility::factory()->create();

        $file = UploadedFile::fake()->image('kerusakan.jpg', 600, 400);

        $payload = [
            'facility_id' => $facility->id,
            'kategori' => 'Kerusakan Fisik',
            'deskripsi' => 'Pintu ruangan rusak dan tidak dapat ditutup rapat.',
            'foto' => $file,
        ];

        $response = $this->actingAs($user)->post(route('reports.store'), $payload);

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'kategori' => 'Kerusakan Fisik',
            'deskripsi' => 'Pintu ruangan rusak dan tidak dapat ditutup rapat.',
            'status' => 'baru',
        ]);

        $report = Report::where('user_id', $user->id)->first();
        $this->assertNotNull($report->foto);
        Storage::disk('public')->assertExists($report->foto);
    }

    public function test_user_can_submit_report_without_photo(): void
    {
        $user = User::factory()->pengguna()->create();
        $facility = Facility::factory()->create();

        $payload = [
            'facility_id' => $facility->id,
            'kategori' => 'Kebersihan',
            'deskripsi' => 'Ruangan kotor setelah kegiatan seminar kemarin sore.',
        ];

        $response = $this->actingAs($user)->post(route('reports.store'), $payload);

        $response->assertRedirect(route('reports.index'));

        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'kategori' => 'Kebersihan',
            'foto' => null,
            'status' => 'baru',
        ]);
    }

    public function test_report_submission_requires_facility_category_and_description(): void
    {
        $user = User::factory()->pengguna()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), []);

        $response->assertSessionHasErrors(['facility_id', 'kategori', 'deskripsi']);
    }

    public function test_photo_must_be_a_valid_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->pengguna()->create();
        $facility = Facility::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'facility_id' => $facility->id,
            'kategori' => 'Lainnya',
            'deskripsi' => 'Deskripsi valid dengan panjang cukup.',
            'foto' => $file,
        ]);

        $response->assertSessionHasErrors(['foto']);
    }
}
