<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Tampilkan katalog fasilitas dengan filter tipe, lokasi, kapasitas, dan status.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $typeFilter = $request->string('tipe')->toString();
        $locationFilter = $request->string('lokasi')->toString();
        $capacityFilter = $request->string('kapasitas')->toString();
        $search = $request->string('search')->toString();
        $kapasitas = $request->filled('kapasitas') && is_numeric($request->query('kapasitas'))
            ? (int) $request->query('kapasitas')
            : null;

        $query = Facility::withCount(['reports' => function ($q) {
            $q->whereIn('status', ['baru', 'diproses']);
        }])->orderBy('nama');

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($typeFilter !== '') {
            $query->where('tipe', $typeFilter);
        }

        if ($locationFilter !== '') {
            $query->where('lokasi', 'like', "%{$locationFilter}%");
        }

        if ($capacityFilter !== '') {
            if ($capacityFilter === 'small') {
                $query->where('kapasitas', '<=', 20);
            } elseif ($capacityFilter === 'medium') {
                $query->whereBetween('kapasitas', [21, 50]);
            } elseif ($capacityFilter === 'large') {
                $query->whereBetween('kapasitas', [51, 100]);
            } elseif ($capacityFilter === 'xlarge') {
                $query->where('kapasitas', '>', 100);
            } elseif (is_numeric($capacityFilter)) {
                $query->where('kapasitas', '>=', (int) $capacityFilter);
            }
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
        $locations = Facility::distinct()->pluck('lokasi')->filter()->values();

        return view('facilities.index', [
            'facilities' => $facilities,
            'types' => $types,
            'locations' => $locations,
            'currentStatus' => $statusFilter,
            'currentType' => $typeFilter,
            'currentLocation' => $locationFilter,
            'currentCapacity' => $capacityFilter,
            'search' => $search,
            'kapasitas' => $kapasitas,
        ]);
    }

    public function show(string $id, Request $request): View
    {
        $facility = Facility::withCount(['reports' => function ($q) {
            $q->whereIn('status', ['baru', 'diproses']);
        }])->findOrFail($id);

        $selectedDate = $request->query('date', $request->query('tanggal', now()->format('Y-m-d')));
        try {
            $parsedDate = Carbon::parse($selectedDate)->format('Y-m-d');
            $selectedDate = $parsedDate;
        } catch (\Exception $e) {
            $selectedDate = now()->format('Y-m-d');
        }

        if ($selectedDate < now()->format('Y-m-d')) {
            $selectedDate = now()->format('Y-m-d');
        }

        $approvedReservations = Reservation::where('facility_id', $facility->id)
            ->where('tanggal', $selectedDate)
            ->where('status', 'disetujui')
            ->get();

        $slots = [];
        $current = Carbon::createFromFormat('H:i', '07:00');
        $endDay = Carbon::createFromFormat('H:i', '20:00');
        $isToday = $selectedDate === now()->format('Y-m-d');
        $currentTime = now()->format('H:i');

        while ($current->lt($endDay)) {
            $slotStart = $current->format('H:i');
            $slotEnd = $current->copy()->addMinutes(30)->format('H:i');

            $isOccupied = $approvedReservations->contains(function ($res) use ($slotStart, $slotEnd) {
                $resStart = substr($res->start_time, 0, 5);
                $resEnd = substr($res->end_time, 0, 5);

                return $resStart < $slotEnd && $resEnd > $slotStart;
            });

            $isPast = $isToday && ($slotStart <= $currentTime);

            $slots[] = [
                'start' => $slotStart,
                'end' => $slotEnd,
                'available' => ! $isOccupied && ! $isPast,
                'is_booked' => $isOccupied,
                'is_past' => $isPast,
            ];

            $current->addMinutes(30);
        }

        $timeOptions = [];
        $t = Carbon::createFromFormat('H:i', '07:00');
        while ($t->lte($endDay)) {
            $timeOptions[] = $t->format('H:i');
            $t->addMinutes(30);
        }

        return view('facilities.show', compact('facility', 'selectedDate', 'slots', 'timeOptions'));
    }
}
