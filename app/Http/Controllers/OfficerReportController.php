<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\Facility;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OfficerReportController extends Controller
{
    /**
     * Display the officer's report queue dashboard (FR-REP-03).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $facilityFilter = $request->integer('facility_id') ?: null;
        $categoryFilter = $request->string('kategori')->toString();
        $search = $request->string('search')->toString();

        $query = Report::with(['facility', 'user'])->latest();

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($facilityFilter !== null) {
            $query->where('facility_id', $facilityFilter);
        }

        if ($categoryFilter !== '') {
            $query->where('kategori', $categoryFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('facility', fn ($fq) => $fq->where('nama', 'like', "%{$search}%"));
            });
        }

        $reports = $query->paginate(12)->withQueryString();

        $counts = [
            'total' => Report::count(),
            'baru' => Report::where('status', 'baru')->count(),
            'diproses' => Report::where('status', 'diproses')->count(),
            'selesai' => Report::where('status', 'selesai')->count(),
            'ditolak' => Report::where('status', 'ditolak')->count(),
        ];

        $facilities = Facility::orderBy('nama')->get();

        $categories = [
            'Kerusakan Fisik',
            'Kelistrikan & Penerangan',
            'Air & Sanitasi (Plumbing)',
            'Kebersihan',
            'Jaringan & Internet',
            'Perangkat / Komputer',
            'Keamanan & Kunci',
            'Lainnya',
        ];

        return view('officer.reports.index', [
            'reports' => $reports,
            'counts' => $counts,
            'facilities' => $facilities,
            'categories' => $categories,
            'currentStatus' => $statusFilter,
            'currentFacility' => $facilityFilter,
            'currentCategory' => $categoryFilter,
            'search' => $search,
        ]);
    }

    /**
     * Update report status and officer notes, with automatic facility status sync (FR-REP-04, FR-REP-05).
     */
    public function updateStatus(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        DB::transaction(function () use ($request, $report) {
            $oldStatus = $report->status;
            $newStatus = $request->string('status')->toString();
            $catatan = $request->filled('catatan') ? $request->string('catatan')->toString() : $report->catatan;

            $report->update([
                'status' => $newStatus,
                'catatan' => $catatan,
            ]);

            // FR-REP-05: Otomatisasi ketersediaan fasilitas
            $facility = $report->facility;

            if ($facility) {
                // Ketika laporan kerusakan sedang ditangani ('diproses'), status fasilitas menjadi 'perbaikan'
                if ($newStatus === 'diproses') {
                    $facility->update(['status' => 'perbaikan']);
                } elseif ($oldStatus === 'diproses' && in_array($newStatus, ['selesai', 'ditolak', 'baru'], true)) {
                    // Cek apakah masih ada laporan aktif lain yang sedang 'diproses' untuk fasilitas ini
                    $hasOtherDiprosesReports = $facility->reports()
                        ->where('id', '!=', $report->id)
                        ->where('status', 'diproses')
                        ->exists();

                    if (! $hasOtherDiprosesReports && $facility->status === 'perbaikan') {
                        $facility->update(['status' => 'tersedia']);
                    }
                }
            }
        });

        return back()->with('success', "Status laporan #{$report->id} berhasil diperbarui menjadi {$report->status_label}.");
    }
}
