<?php

namespace Tests\Feature;

use Database\Seeders\RekapDummySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRekapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RekapDummySeeder::class);
    }

    public function test_dashboard_rekap_is_accessible(): void
    {
        $response = $this->get(route('admin.rekap.index'));
        $response->assertStatus(200);
        $response->assertSee('Executive Rekap Dashboard');
    }

    public function test_rekap_okupansi_page_is_accessible(): void
    {
        $response = $this->get(route('admin.rekap.okupansi'));
        $response->assertStatus(200);
        $response->assertSee('Rekap Okupansi Fasilitas');
    }

    public function test_rekap_kerusakan_page_is_accessible(): void
    {
        $response = $this->get(route('admin.rekap.kerusakan'));
        $response->assertStatus(200);
        $response->assertSee('Rekap Frekuensi Kerusakan');
    }

    public function test_export_okupansi_csv(): void
    {
        $response = $this->get(route('admin.rekap.okupansi.export', ['format' => 'csv']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_export_okupansi_excel(): void
    {
        $response = $this->get(route('admin.rekap.okupansi.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-excel', $response->headers->get('content-type'));
    }

    public function test_export_okupansi_pdf(): void
    {
        $response = $this->get(route('admin.rekap.okupansi.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_export_kerusakan_csv(): void
    {
        $response = $this->get(route('admin.rekap.kerusakan.export', ['format' => 'csv']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_export_kerusakan_excel(): void
    {
        $response = $this->get(route('admin.rekap.kerusakan.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-excel', $response->headers->get('content-type'));
    }

    public function test_export_kerusakan_pdf(): void
    {
        $response = $this->get(route('admin.rekap.kerusakan.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
