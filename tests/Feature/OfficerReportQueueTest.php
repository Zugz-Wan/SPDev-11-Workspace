<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficerReportQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_officer_cannot_access_officer_queue(): void
    {
        $user = User::factory()->pengguna()->create();

        $response = $this->actingAs($user)->get(route('officer.reports.index'));

        $response->assertStatus(403);
    }

    public function test_officer_can_view_all_incoming_and_in_progress_reports(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create(['nama' => 'Lab Biologi']);

        $reportBaru = Report::factory()->create([
            'facility_id' => $facility->id,
            'status' => 'baru',
            'deskripsi' => 'Antrian laporan masuk baru.',
        ]);

        $reportDiproses = Report::factory()->diproses()->create([
            'facility_id' => $facility->id,
            'deskripsi' => 'Laporan sedang ditangani teknisi.',
        ]);

        $response = $this->actingAs($petugas)->get(route('officer.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Antrian Laporan Kerusakan Fasilitas');
        $response->assertSee('Antrian laporan masuk baru.');
        $response->assertSee('Laporan sedang ditangani teknisi.');
        $response->assertSee('Lab Biologi');
    }

    public function test_officer_can_filter_queue_by_status(): void
    {
        $petugas = User::factory()->petugas()->create();

        Report::factory()->create([
            'status' => 'baru',
            'deskripsi' => 'Laporan BARU XYZ.',
        ]);

        Report::factory()->diproses()->create([
            'status' => 'diproses',
            'deskripsi' => 'Laporan DIPROSES ABC.',
        ]);

        $response = $this->actingAs($petugas)->get(route('officer.reports.index', ['status' => 'diproses']));

        $response->assertStatus(200);
        $response->assertSee('Laporan DIPROSES ABC.');
        $response->assertDontSee('Laporan BARU XYZ.');
    }
}
