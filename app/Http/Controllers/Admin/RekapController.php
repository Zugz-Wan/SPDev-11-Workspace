<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapController extends Controller
{
    /**
     * Dashboard Overview Rekap
     */
    public function index()
    {
        $totalFacilities = Facility::count();
        $totalReservations = Reservation::count();
        $approvedReservations = Reservation::where('status', 'disetujui')->count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        
        $totalReports = Report::count();
        $waitingReports = Report::where('status', 'menunggu')->count();
        $inProgressReports = Report::where('status', 'diproses')->count();
        $doneReports = Report::where('status', 'selesai')->count();

        // Top 3 Fasilitas Paling Sering Digunakan
        $topOccupancy = Facility::withCount(['reservations' => function ($q) {
            $q->where('status', 'disetujui');
        }])->orderByDesc('reservations_count')->take(4)->get();

        // Top 3 Fasilitas Paling Sering Mengalami Kerusakan
        $topDamage = Facility::withCount('reports')
            ->orderByDesc('reports_count')
            ->take(4)
            ->get();

        return view('admin.rekap.index', compact(
            'totalFacilities',
            'totalReservations',
            'approvedReservations',
            'pendingReservations',
            'totalReports',
            'waitingReports',
            'inProgressReports',
            'doneReports',
            'topOccupancy',
            'topDamage'
        ));
    }

    /**
     * FR-ADM-01: Rekap Okupansi Fasilitas Lintas Periode
     */
    public function okupansi(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->addDays(15)->format('Y-m-d'));
        $tipe = $request->input('tipe');
        $facilityId = $request->input('facility_id');

        $data = $this->getOccupancyData($startDate, $endDate, $tipe, $facilityId);

        $facilitiesList = Facility::orderBy('nama')->get();
        $tipeOptions = ['ruangan', 'lapangan', 'aula', 'laboratorium'];

        return view('admin.rekap.okupansi', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedTipe' => $tipe,
            'selectedFacilityId' => $facilityId,
            'facilitiesList' => $facilitiesList,
            'tipeOptions' => $tipeOptions,
        ]));
    }

    /**
     * FR-ADM-02: Rekap Frekuensi Kerusakan per Fasilitas & Lokasi
     */
    public function kerusakan(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(60)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $facilityId = $request->input('facility_id');
        $lokasi = $request->input('lokasi');
        $status = $request->input('status');

        $data = $this->getDamageData($startDate, $endDate, $facilityId, $lokasi, $status);

        $facilitiesList = Facility::orderBy('nama')->get();
        $lokasiList = Facility::select('lokasi')->distinct()->pluck('lokasi');

        return view('admin.rekap.kerusakan', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedFacilityId' => $facilityId,
            'selectedLokasi' => $lokasi,
            'selectedStatus' => $status,
            'facilitiesList' => $facilitiesList,
            'lokasiList' => $lokasiList,
        ]));
    }

    /**
     * FR-ADM-03: Ekspor Rekap Okupansi (CSV, Excel, PDF)
     */
    public function exportOkupansi(Request $request, string $format)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->addDays(15)->format('Y-m-d'));
        $tipe = $request->input('tipe');
        $facilityId = $request->input('facility_id');

        $data = $this->getOccupancyData($startDate, $endDate, $tipe, $facilityId);
        $filename = 'rekap_okupansi_fasilitas_' . date('Ymd_His');

        if ($format === 'csv') {
            return $this->exportOccupancyCsv($data, $filename . '.csv');
        } elseif ($format === 'excel') {
            return $this->exportOccupancyExcel($data, $filename . '.xls');
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.rekap.pdf.okupansi', array_merge($data, [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]))->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        abort(404, 'Format ekspor tidak didukung.');
    }

    /**
     * FR-ADM-03: Ekspor Rekap Kerusakan (CSV, Excel, PDF)
     */
    public function exportKerusakan(Request $request, string $format)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(60)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $facilityId = $request->input('facility_id');
        $lokasi = $request->input('lokasi');
        $status = $request->input('status');

        $data = $this->getDamageData($startDate, $endDate, $facilityId, $lokasi, $status);
        $filename = 'rekap_frekuensi_kerusakan_' . date('Ymd_His');

        if ($format === 'csv') {
            return $this->exportDamageCsv($data, $filename . '.csv');
        } elseif ($format === 'excel') {
            return $this->exportDamageExcel($data, $filename . '.xls');
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.rekap.pdf.kerusakan', array_merge($data, [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]))->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        abort(404, 'Format ekspor tidak didukung.');
    }

    // ==========================================
    // LOGIKA PERHITUNGAN & PENGAMBILAN DATA
    // ==========================================

    private function getOccupancyData(string $startDate, string $endDate, ?string $tipe, ?string $facilityId): array
    {
        $daysDiff = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $operationalHoursPerDay = 12; // Asumsi jam operasional 07:00 - 19:00 = 12 jam/hari
        $maxPotentialHours = $daysDiff * $operationalHoursPerDay;

        // Query fasilitas
        $facQuery = Facility::query();
        if ($tipe) {
            $facQuery->where('tipe', $tipe);
        }
        if ($facilityId) {
            $facQuery->where('id', $facilityId);
        }
        $facilities = $facQuery->orderBy('nama')->get();

        // Query reservasi disetujui dalam rentang tanggal
        $resQuery = Reservation::with(['facility', 'user'])
            ->where('status', 'disetujui')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($tipe) {
            $resQuery->whereHas('facility', fn($q) => $q->where('tipe', $tipe));
        }
        if ($facilityId) {
            $resQuery->where('facility_id', $facilityId);
        }
        $reservations = $resQuery->orderBy('tanggal')->orderBy('start_time')->get();

        // Agregasi per fasilitas
        $occupancySummary = [];
        $totalHoursAll = 0;
        $totalApprovedReservations = 0;

        foreach ($facilities as $facility) {
            $facReservations = $reservations->where('facility_id', $facility->id);
            $totalHours = 0;

            foreach ($facReservations as $res) {
                $start = Carbon::parse($res->start_time);
                $end = Carbon::parse($res->end_time);
                $duration = max(0, $end->diffInHours($start));
                $totalHours += $duration;
            }

            $occupancyRate = ($maxPotentialHours > 0) 
                ? min(100, round(($totalHours / $maxPotentialHours) * 100, 1)) 
                : 0;

            $totalHoursAll += $totalHours;
            $totalApprovedReservations += $facReservations->count();

            $occupancySummary[] = [
                'facility' => $facility,
                'total_reservations' => $facReservations->count(),
                'total_hours' => $totalHours,
                'max_hours' => $maxPotentialHours,
                'occupancy_rate' => $occupancyRate,
            ];
        }

        // Urutkan fasilitas dari okupansi tertinggi
        usort($occupancySummary, fn($a, $b) => $b['total_hours'] <=> $a['total_hours']);

        return [
            'occupancySummary' => $occupancySummary,
            'reservations' => $reservations,
            'totalHoursAll' => $totalHoursAll,
            'totalApprovedReservations' => $totalApprovedReservations,
            'daysDiff' => $daysDiff,
        ];
    }

    private function getDamageData(string $startDate, string $endDate, ?string $facilityId, ?string $lokasi, ?string $status): array
    {
        $reportQuery = Report::with(['facility', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($facilityId) {
            $reportQuery->where('facility_id', $facilityId);
        }
        if ($lokasi) {
            $reportQuery->whereHas('facility', fn($q) => $q->where('lokasi', 'like', "%{$lokasi}%"));
        }
        if ($status) {
            $reportQuery->where('status', $status);
        }

        $reports = $reportQuery->orderByDesc('created_at')->get();

        // Agregasi per Fasilitas
        $byFacility = [];
        // Agregasi per Lokasi
        $byLocation = [];

        foreach ($reports as $r) {
            $facName = $r->facility->nama ?? 'Tanpa Nama';
            $facLoc = $r->facility->lokasi ?? 'Tanpa Lokasi';
            $facTipe = $r->facility->tipe ?? '-';

            // Per Fasilitas
            if (!isset($byFacility[$r->facility_id])) {
                $byFacility[$r->facility_id] = [
                    'nama' => $facName,
                    'lokasi' => $facLoc,
                    'tipe' => $facTipe,
                    'total' => 0,
                    'menunggu' => 0,
                    'diproses' => 0,
                    'selesai' => 0,
                ];
            }
            $byFacility[$r->facility_id]['total']++;
            $byFacility[$r->facility_id][$r->status]++;

            // Per Lokasi
            if (!isset($byLocation[$facLoc])) {
                $byLocation[$facLoc] = [
                    'lokasi' => $facLoc,
                    'total' => 0,
                    'menunggu' => 0,
                    'diproses' => 0,
                    'selesai' => 0,
                ];
            }
            $byLocation[$facLoc]['total']++;
            $byLocation[$facLoc][$r->status]++;
        }

        // Urutkan frekuensi tertinggi
        usort($byFacility, fn($a, $b) => $b['total'] <=> $a['total']);
        usort($byLocation, fn($a, $b) => $b['total'] <=> $a['total']);

        return [
            'reports' => $reports,
            'damageByFacility' => $byFacility,
            'damageByLocation' => $byLocation,
            'totalReports' => $reports->count(),
            'totalWaiting' => $reports->where('status', 'menunggu')->count(),
            'totalInProgress' => $reports->where('status', 'diproses')->count(),
            'totalDone' => $reports->where('status', 'selesai')->count(),
        ];
    }

    // ==========================================
    // EXPORT FORMAT IMPLEMENTATIONS
    // ==========================================

    private function exportOccupancyCsv(array $data, string $filename): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM agar rapi saat dibuka di MS Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header Ringkasan
            fputcsv($handle, ['REKAP OKUPANSI FASILITAS']);
            fputcsv($handle, ['No', 'Nama Fasilitas', 'Tipe', 'Lokasi', 'Kapasitas', 'Total Reservasi Disetujui', 'Total Durasi (Jam)', 'Tingkat Okupansi (%)']);

            $no = 1;
            foreach ($data['occupancySummary'] as $item) {
                fputcsv($handle, [
                    $no++,
                    $item['facility']->nama,
                    ucfirst($item['facility']->tipe),
                    $item['facility']->lokasi,
                    $item['facility']->kapasitas . ' Orang',
                    $item['total_reservations'],
                    $item['total_hours'],
                    $item['occupancy_rate'] . '%',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['RINCIAN LOG RESERVASI DISETUJUI']);
            fputcsv($handle, ['No', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Fasilitas', 'Peminjam', 'Keperluan']);

            $no = 1;
            foreach ($data['reservations'] as $res) {
                fputcsv($handle, [
                    $no++,
                    $res->tanggal,
                    substr($res->start_time, 0, 5),
                    substr($res->end_time, 0, 5),
                    $res->facility->nama ?? '-',
                    $res->user->name ?? '-',
                    $res->keperluan,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function exportOccupancyExcel(array $data, string $filename)
    {
        $html = view('admin.rekap.excel.okupansi', $data)->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function exportDamageCsv(array $data, string $filename): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['REKAP FREKUENSI KERUSAKAN PER FASILITAS']);
            fputcsv($handle, ['No', 'Nama Fasilitas', 'Tipe', 'Lokasi', 'Total Kerusakan', 'Menunggu', 'Diproses', 'Selesai']);

            $no = 1;
            foreach ($data['damageByFacility'] as $item) {
                fputcsv($handle, [
                    $no++,
                    $item['nama'],
                    ucfirst($item['tipe']),
                    $item['lokasi'],
                    $item['total'],
                    $item['menunggu'],
                    $item['diproses'],
                    $item['selesai'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['REKAP FREKUENSI KERUSAKAN PER LOKASI']);
            fputcsv($handle, ['No', 'Lokasi', 'Total Kerusakan', 'Menunggu', 'Diproses', 'Selesai']);

            $no = 1;
            foreach ($data['damageByLocation'] as $item) {
                fputcsv($handle, [
                    $no++,
                    $item['lokasi'],
                    $item['total'],
                    $item['menunggu'],
                    $item['diproses'],
                    $item['selesai'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['LOG DETAIL LAPORAN KERUSAKAN']);
            fputcsv($handle, ['No', 'Tanggal Lapor', 'Fasilitas', 'Lokasi', 'Pelapor', 'Deskripsi Masalah', 'Status']);

            $no = 1;
            foreach ($data['reports'] as $rep) {
                fputcsv($handle, [
                    $no++,
                    $rep->created_at->format('Y-m-d H:i'),
                    $rep->facility->nama ?? '-',
                    $rep->facility->lokasi ?? '-',
                    $rep->user->name ?? '-',
                    $rep->deskripsi,
                    ucfirst($rep->status),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function exportDamageExcel(array $data, string $filename)
    {
        $html = view('admin.rekap.excel.kerusakan', $data)->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
