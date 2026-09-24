<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficerUpdateReportStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_can_update_report_status_and_add_notes(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create(['status' => 'tersedia']);
        $report = Report::factory()->create([
            'facility_id' => $facility->id,
            'status' => 'baru',
            'catatan' => null,
        ]);

        $response = $this->actingAs($petugas)->patch(route('officer.reports.update-status', $report), [
            'status' => 'diproses',
            'catatan' => 'Teknisi ditugaskan untuk mengecek panel listrik.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'diproses',
            'catatan' => 'Teknisi ditugaskan untuk mengecek panel listrik.',
        ]);
    }

    public function test_facility_status_automatically_updates_to_perbaikan_when_report_is_in_progress(): void
    {
        // FR-REP-05: Sistem secara otomatis memperbarui status ketersediaan fasilitas
        // menjadi "dalam perbaikan" ketika laporan kerusakan sedang ditangani
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create(['status' => 'tersedia']);
        $report = Report::factory()->create([
            'facility_id' => $facility->id,
            'status' => 'baru',
        ]);

        $this->assertEquals('tersedia', $facility->fresh()->status);

        $this->actingAs($petugas)->patch(route('officer.reports.update-status', $report), [
            'status' => 'diproses',
            'catatan' => 'Mulai perbaikan fasilitas.',
        ]);

        $this->assertEquals('perbaikan', $facility->fresh()->status);
    }

    public function test_facility_status_automatically_reverts_to_tersedia_when_all_reports_are_resolved(): void
    {
        // FR-REP-05: Ketika laporan selesai/ditolak dan tidak ada lagi yang diproses, status fasilitas kembali tersedia
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create(['status' => 'perbaikan']);
        $report = Report::factory()->diproses()->create([
            'facility_id' => $facility->id,
        ]);

        $this->actingAs($petugas)->patch(route('officer.reports.update-status', $report), [
            'status' => 'selesai',
            'catatan' => 'Perbaikan sudah selesai dan teruji.',
        ]);

        $this->assertEquals('tersedia', $facility->fresh()->status);
    }

    public function test_facility_status_remains_perbaikan_if_another_report_is_still_in_progress(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create(['status' => 'perbaikan']);

        $report1 = Report::factory()->diproses()->create([
            'facility_id' => $facility->id,
        ]);

        $report2 = Report::factory()->diproses()->create([
            'facility_id' => $facility->id,
        ]);

        // Selesaikan report1, report2 masih diproses
        $this->actingAs($petugas)->patch(route('officer.reports.update-status', $report1), [
            'status' => 'selesai',
            'catatan' => 'Masalah pertama sudah selesai.',
        ]);

        $this->assertEquals('perbaikan', $facility->fresh()->status);
    }

    public function test_non_officer_cannot_update_report_status(): void
    {
        $user = User::factory()->pengguna()->create();
        $report = Report::factory()->create(['status' => 'baru']);

        $response = $this->actingAs($user)->patch(route('officer.reports.update-status', $report), [
            'status' => 'selesai',
            'catatan' => 'Upaya pembaruan ilegal.',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('baru', $report->fresh()->status);
    }

    public function test_update_report_status_requires_valid_status_value(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create(['status' => 'baru']);

        $response = $this->actingAs($petugas)->patch(route('officer.reports.update-status', $report), [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertEquals('baru', $report->fresh()->status);
    }
}
