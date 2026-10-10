<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapController extends Controller
{
    /**
     * Tampilkan halaman rekapitulasi okupansi fasilitas dan frekuensi kerusakan.
     */
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date') ? $request->query('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->query('end_date') : null;

        $data = $this->buildRekapData($startDate, $endDate);

        return view('admin.rekap.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'occupancyData' => $data['occupancyData'],
            'damageFacilityData' => $data['damageFacilityData'],
            'damageLocationData' => $data['damageLocationData'],
            'summary' => $data['summary'],
            'periodeLabel' => $data['periodeLabel'],
        ]);
    }

    /**
     * Tampilan cetak / print-friendly untuk ekspor ke PDF via browser print.
     */
    public function print(Request $request): View
    {
        $startDate = $request->filled('start_date') ? $request->query('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->query('end_date') : null;

        $data = $this->buildRekapData($startDate, $endDate);

        return view('admin.rekap.print', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'occupancyData' => $data['occupancyData'],
            'damageFacilityData' => $data['damageFacilityData'],
            'damageLocationData' => $data['damageLocationData'],
            'summary' => $data['summary'],
            'periodeLabel' => $data['periodeLabel'],
            'printedAt' => now(),
            'printedBy' => auth()->user(),
        ]);
    }

    /**
     * Ekspor data rekap ke format CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->filled('start_date') ? $request->query('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->query('end_date') : null;
        $type = $request->query('type', 'all');

        $data = $this->buildRekapData($startDate, $endDate);

        $dateSuffix = ($startDate ?? 'semua').'-sd-'.($endDate ?? now()->format('Y-m-d'));
        $filename = "rekap-{$type}-{$dateSuffix}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($data, $type) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'occupancy' || $type === 'all') {
                fputcsv($handle, ['=== REKAP OKUPANSI FASILITAS (RESERVASI DISETUJUI & JAM TERPAKAI) ===']);
                fputcsv($handle, ['Periode Laporan:', $data['periodeLabel']]);
                fputcsv($handle, [
                    'No',
                    'Nama Fasilitas',
                    'Tipe Fasilitas',
                    'Lokasi',
                    'Kapasitas (Orang)',
                    'Status Fasilitas',
                    'Jumlah Reservasi Disetujui',
                    'Total Jam Terpakai (Jam)',
                    'Total Menit Terpakai',
                    'Rata-rata Durasi (Jam/Reservasi)',
                ]);

                foreach ($data['occupancyData'] as $index => $row) {
                    fputcsv($handle, [
                        $index + 1,
                        $row['facility']->nama,
                        ucfirst($row['facility']->tipe),
                        $row['facility']->lokasi,
                        $row['facility']->kapasitas,
                        $row['facility']->status_label,
                        $row['approved_count'],
                        $row['total_hours'],
                        $row['total_minutes'],
                        $row['avg_hours'],
                    ]);
                }
                fputcsv($handle, []);
            }

            if ($type === 'damage_facility' || $type === 'all') {
                fputcsv($handle, ['=== REKAP FREKUENSI KERUSAKAN PER FASILITAS ===']);
                fputcsv($handle, ['Periode Laporan:', $data['periodeLabel']]);
                fputcsv($handle, [
                    'No',
                    'Nama Fasilitas',
                    'Tipe Fasilitas',
                    'Lokasi',
                    'Status Fasilitas',
                    'Total Laporan Kerusakan',
                    'Status: Baru',
                    'Status: Diproses',
                    'Status: Selesai',
                    'Status: Ditolak',
                    'Tingkat Penyelesaian (%)',
                ]);

                foreach ($data['damageFacilityData'] as $index => $row) {
                    fputcsv($handle, [
                        $index + 1,
                        $row['facility']->nama,
                        ucfirst($row['facility']->tipe),
                        $row['facility']->lokasi,
                        $row['facility']->status_label,
                        $row['total_reports'],
                        $row['baru_count'],
                        $row['diproses_count'],
                        $row['selesai_count'],
                        $row['ditolak_count'],
                        $row['resolution_rate'].'%',
                    ]);
                }
                fputcsv($handle, []);
            }

            if ($type === 'damage_location' || $type === 'all') {
                fputcsv($handle, ['=== REKAP FREKUENSI KERUSAKAN PER LOKASI ===']);
                fputcsv($handle, ['Periode Laporan:', $data['periodeLabel']]);
                fputcsv($handle, [
                    'No',
                    'Nama Lokasi',
                    'Jumlah Fasilitas',
                    'Daftar Fasilitas',
                    'Total Laporan Kerusakan',
                    'Laporan Baru',
                    'Laporan Diproses',
                    'Laporan Selesai',
                    'Laporan Ditolak',
                    'Total Belum Selesai (Baru + Diproses)',
                ]);

                foreach ($data['damageLocationData'] as $index => $row) {
                    fputcsv($handle, [
                        $index + 1,
                        $row['lokasi'],
                        $row['facilities_count'],
                        $row['facility_names'],
                        $row['total_reports'],
                        $row['baru_count'],
                        $row['diproses_count'],
                        $row['selesai_count'],
                        $row['ditolak_count'],
                        $row['pending_count'],
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Hitung dataset rekapitulasi okupansi dan frekuensi kerusakan.
     *
     * @return array{
     *     occupancyData: Collection<int, array<string, mixed>>,
     *     damageFacilityData: Collection<int, array<string, mixed>>,
     *     damageLocationData: Collection<int, array<string, mixed>>,
     *     summary: array<string, mixed>,
     *     periodeLabel: string
     * }
     */
    private function buildRekapData(?string $startDate, ?string $endDate): array
    {
        $facilities = Facility::with([
            'reservations' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'disetujui');
                if ($startDate) {
                    $query->where('tanggal', '>=', $startDate);
                }
                if ($endDate) {
                    $query->where('tanggal', '<=', $endDate);
                }
            },
            'reports' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
            },
        ])->get();

        // 1. Okupansi per Fasilitas (Jumlah Reservasi Disetujui & Total Jam Terpakai)
        $occupancyData = $facilities->map(function (Facility $facility) {
            $approvedCount = $facility->reservations->count();
            $totalMinutes = (int) $facility->reservations->sum(function (Reservation $res) {
                try {
                    $start = Carbon::createFromTimeString($res->start_time);
                    $end = Carbon::createFromTimeString($res->end_time);
                    $diff = $start->diffInMinutes($end, false);

                    return max(0, $diff);
                } catch (\Throwable) {
                    return 0;
                }
            });

            $totalHours = round($totalMinutes / 60, 2);
            $avgHours = $approvedCount > 0 ? round($totalHours / $approvedCount, 2) : 0.0;

            $hoursPart = floor($totalMinutes / 60);
            $minsPart = $totalMinutes % 60;
            $formattedDuration = $totalMinutes === 0
                ? '0 jam'
                : ($minsPart > 0 ? "{$hoursPart} jam {$minsPart} mnt" : "{$hoursPart} jam");

            return [
                'facility' => $facility,
                'approved_count' => $approvedCount,
                'total_minutes' => $totalMinutes,
                'total_hours' => $totalHours,
                'formatted_duration' => $formattedDuration,
                'avg_hours' => $avgHours,
            ];
        })->sortByDesc('total_hours')->values();

        // 2. Frekuensi Kerusakan per Fasilitas
        $damageFacilityData = $facilities->map(function (Facility $facility) {
            $totalReports = $facility->reports->count();
            $baruCount = $facility->reports->where('status', 'baru')->count();
            $diprosesCount = $facility->reports->where('status', 'diproses')->count();
            $selesaiCount = $facility->reports->where('status', 'selesai')->count();
            $ditolakCount = $facility->reports->where('status', 'ditolak')->count();
            $resolutionRate = $totalReports > 0 ? round(($selesaiCount / $totalReports) * 100, 1) : 0.0;

            return [
                'facility' => $facility,
                'total_reports' => $totalReports,
                'baru_count' => $baruCount,
                'diproses_count' => $diprosesCount,
                'selesai_count' => $selesaiCount,
                'ditolak_count' => $ditolakCount,
                'resolution_rate' => $resolutionRate,
            ];
        })->sortByDesc('total_reports')->values();

        // 3. Frekuensi Kerusakan per Lokasi
        $damageLocationData = $facilities->groupBy('lokasi')->map(function (Collection $facilitiesInLocation, string $lokasi) {
            $allReports = $facilitiesInLocation->flatMap->reports;
            $totalReports = $allReports->count();
            $baruCount = $allReports->where('status', 'baru')->count();
            $diprosesCount = $allReports->where('status', 'diproses')->count();
            $selesaiCount = $allReports->where('status', 'selesai')->count();
            $ditolakCount = $allReports->where('status', 'ditolak')->count();

            return [
                'lokasi' => $lokasi,
                'facilities_count' => $facilitiesInLocation->count(),
                'facility_names' => $facilitiesInLocation->pluck('nama')->implode(', '),
                'total_reports' => $totalReports,
                'baru_count' => $baruCount,
                'diproses_count' => $diprosesCount,
                'selesai_count' => $selesaiCount,
                'ditolak_count' => $ditolakCount,
                'pending_count' => $baruCount + $diprosesCount,
            ];
        })->sortByDesc('total_reports')->values();

        // 4. Ringkasan Metrik
        $totalFacilities = $facilities->count();
        $totalApprovedReservations = (int) $occupancyData->sum('approved_count');
        $totalHoursUsed = round((float) $occupancyData->sum('total_hours'), 2);
        $totalReports = (int) $damageFacilityData->sum('total_reports');
        $totalReportsResolved = (int) $damageFacilityData->sum('selesai_count');
        $overallResolutionRate = $totalReports > 0 ? round(($totalReportsResolved / $totalReports) * 100, 1) : 0.0;

        $topOccupancy = $occupancyData->firstWhere('total_hours', '>', 0);
        $topDamage = $damageFacilityData->firstWhere('total_reports', '>', 0);

        $summary = [
            'total_facilities' => $totalFacilities,
            'total_approved_reservations' => $totalApprovedReservations,
            'total_hours_used' => $totalHoursUsed,
            'total_reports' => $totalReports,
            'total_reports_resolved' => $totalReportsResolved,
            'overall_resolution_rate' => $overallResolutionRate,
            'top_occupancy' => $topOccupancy,
            'top_damage' => $topDamage,
        ];

        // 5. Label Periode
        if ($startDate && $endDate) {
            $periodeLabel = Carbon::parse($startDate)->translatedFormat('d M Y').' - '.Carbon::parse($endDate)->translatedFormat('d M Y');
        } elseif ($startDate) {
            $periodeLabel = 'Mulai '.Carbon::parse($startDate)->translatedFormat('d M Y');
        } elseif ($endDate) {
            $periodeLabel = 'Hingga '.Carbon::parse($endDate)->translatedFormat('d M Y');
        } else {
            $periodeLabel = 'Semua Periode';
        }

        return [
            'occupancyData' => $occupancyData,
            'damageFacilityData' => $damageFacilityData,
            'damageLocationData' => $damageLocationData,
            'summary' => $summary,
            'periodeLabel' => $periodeLabel,
        ];
    }
}
