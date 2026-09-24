<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Facility;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display a listing of reports submitted by the authenticated user (FR-REP-02).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $categoryFilter = $request->string('kategori')->toString();

        $reports = $request->user()
            ->reports()
            ->with('facility')
            ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter))
            ->when($categoryFilter !== '', fn ($query) => $query->where('kategori', $categoryFilter))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'total' => $request->user()->reports()->count(),
            'baru' => $request->user()->reports()->where('status', 'baru')->count(),
            'diproses' => $request->user()->reports()->where('status', 'diproses')->count(),
            'selesai' => $request->user()->reports()->where('status', 'selesai')->count(),
            'ditolak' => $request->user()->reports()->where('status', 'ditolak')->count(),
        ];

        return view('reports.index', [
            'reports' => $reports,
            'counts' => $counts,
            'currentStatus' => $statusFilter,
            'currentCategory' => $categoryFilter,
        ]);
    }

    /**
     * Show the form for creating a new report (FR-REP-01).
     */
    public function create(Request $request): View
    {
        $facilities = Facility::where('status', '!=', 'tidak_aktif')->orderBy('nama')->get();
        $selectedFacilityId = $request->integer('facility_id') ?: null;

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

        return view('reports.create', [
            'facilities' => $facilities,
            'selectedFacilityId' => $selectedFacilityId,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created report in storage (FR-REP-01).
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('reports', 'public');
        }

        $report = $request->user()->reports()->create([
            'facility_id' => $request->integer('facility_id'),
            'kategori' => $request->string('kategori')->toString(),
            'deskripsi' => $request->string('deskripsi')->toString(),
            'foto' => $fotoPath,
            'status' => 'baru',
        ]);

        return redirect()
            ->route('reports.index')
            ->with('success', "Laporan kerusakan fasilitas berhasil diajukan dengan ID #{$report->id}. Petugas akan segera meninjau laporan Anda.");
    }

    /**
     * Display the specified report.
     */
    public function show(Request $request, Report $report): View
    {
        if (! $request->user()->isPetugas() && $report->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak untuk melihat laporan ini.');
        }

        $report->load(['facility', 'user']);

        return view('reports.show', [
            'report' => $report,
        ]);
    }
}
