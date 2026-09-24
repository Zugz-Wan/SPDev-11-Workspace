<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_submitted_reports_and_statuses(): void
    {
        $user = User::factory()->pengguna()->create();
        $facility = Facility::factory()->create(['nama' => 'Ruang Teater']);

        $report1 = Report::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'kategori' => 'Kelistrikan & Penerangan',
            'deskripsi' => 'Lampu panggung padam sebagian.',
            'status' => 'baru',
        ]);

        $report2 = Report::factory()->diproses()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'kategori' => 'Kerusakan Fisik',
            'deskripsi' => 'Kursi baris ke-3 patah sandarannya.',
            'catatan' => 'Petugas sedang menyiapkan suku cadang kursi baru.',
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Ruang Teater');
        $response->assertSee('Lampu panggung padam sebagian.');
        $response->assertSee('Kursi baris ke-3 patah sandarannya.');
        $response->assertSee('Baru');
        $response->assertSee('Diproses');
        $response->assertSee('Petugas sedang menyiapkan suku cadang kursi baru.');
    }

    public function test_user_cannot_see_reports_belonging_to_other_users(): void
    {
        $userA = User::factory()->pengguna()->create(['name' => 'User A']);
        $userB = User::factory()->pengguna()->create(['name' => 'User B']);

        $reportA = Report::factory()->create([
            'user_id' => $userA->id,
            'deskripsi' => 'Laporan khusus milik User A rahasia.',
        ]);

        $reportB = Report::factory()->create([
            'user_id' => $userB->id,
            'deskripsi' => 'Laporan milik User B yang tidak boleh dilihat User A.',
        ]);

        $response = $this->actingAs($userA)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan khusus milik User A rahasia.');
        $response->assertDontSee('Laporan milik User B yang tidak boleh dilihat User A.');
    }

    public function test_user_can_filter_reports_by_status(): void
    {
        $user = User::factory()->pengguna()->create();

        Report::factory()->create([
            'user_id' => $user->id,
            'deskripsi' => 'Laporan status baru 12345.',
            'status' => 'baru',
        ]);

        Report::factory()->selesai()->create([
            'user_id' => $user->id,
            'deskripsi' => 'Laporan status selesai 67890.',
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', ['status' => 'selesai']));

        $response->assertStatus(200);
        $response->assertSee('Laporan status selesai 67890.');
        $response->assertDontSee('Laporan status baru 12345.');
    }
}
