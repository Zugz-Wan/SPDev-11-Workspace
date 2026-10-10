<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecapController extends Controller
{
    /**
     * Tampilkan rekap okupansi fasilitas dan frekuensi kerusakan (US 17).
     */
    public function index(Request $request): View
    {
        $facilities = Facility::withCount([
            'reservations as total_reservations' => function ($q) {
                $q->where('status', 'disetujui');
            },
            'reports as total_reports',
            'reports as active_damage_reports' => function ($q) {
                $q->whereIn('status', ['baru', 'diproses']);
            },
            'reports as resolved_reports' => function ($q) {
                $q->where('status', 'selesai');
            },
        ])->get();

        $totalReservations = Reservation::where('status', 'disetujui')->count();
        $totalReports = Report::count();
        $totalFacilities = Facility::count();

        return view('admin.recap.index', compact('facilities', 'totalReservations', 'totalReports', 'totalFacilities'));
    }

    /**
     * Ekspor data rekapitulasi ke format CSV (US 17).
     */
    public function exportCsv(): StreamedResponse
    {
        $filename = 'rekap_fasilitas_kampus_'.date('Ymd_His').'.csv';

        $facilities = Facility::withCount([
            'reservations as total_reservations' => function ($q) {
                $q->where('status', 'disetujui');
            },
            'reports as total_reports',
            'reports as active_damage_reports' => function ($q) {
                $q->whereIn('status', ['baru', 'diproses']);
            },
            'reports as resolved_reports' => function ($q) {
                $q->where('status', 'selesai');
            },
        ])->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($facilities) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8 compatibility in Excel
            fwrite($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'ID',
                'Nama Fasilitas',
                'Tipe',
                'Lokasi',
                'Kapasitas',
                'Status Saat Ini',
                'Total Reservasi Disetujui',
                'Total Laporan Kerusakan',
                'Laporan Dalam Penanganan',
                'Laporan Selesai Diperbaiki',
            ]);

            foreach ($facilities as $facility) {
                fputcsv($file, [
                    $facility->id,
                    $facility->nama,
                    ucfirst($facility->tipe),
                    $facility->lokasi,
                    $facility->kapasitas,
                    ucfirst(str_replace('_', ' ', $facility->status)),
                    $facility->total_reservations,
                    $facility->total_reports,
                    $facility->active_damage_reports,
                    $facility->resolved_reports,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
