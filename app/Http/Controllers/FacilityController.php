<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities and their current status (FR-REP-05 visibility).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $typeFilter = $request->string('tipe')->toString();
        $search = $request->string('search')->toString();

        $query = Facility::withCount(['reports' => function ($q) {
            $q->whereIn('status', ['baru', 'diproses']);
        }])->orderBy('nama');

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($typeFilter !== '') {
            $query->where('tipe', $typeFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $facilities = $query->paginate(12)->withQueryString();

        $types = Facility::distinct()->pluck('tipe')->filter()->values();

        return view('facilities.index', [
            'facilities' => $facilities,
            'types' => $types,
            'currentStatus' => $statusFilter,
            'currentType' => $typeFilter,
            'search' => $search,
        ]);
    }
}
