<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasReservationController extends Controller
{
    /**
     * Tampilkan daftar reservasi untuk petugas.
     * Bisa difilter berdasarkan status (default: pending).
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = Reservation::with(['user', 'facility']);

        if ($status !== 'semua' && $status !== '') {
            $query->where('status', $status);
        }

        if ($request->wantsJson()) {
            return response()->json($query->latest()->get());
        }

        $reservations = $query->latest('tanggal')->latest('start_time')->paginate(12)->withQueryString();

        $pendingCount = Reservation::where('status', 'pending')->count();
        $approvedCount = Reservation::where('status', 'disetujui')->count();
        $rejectedCount = Reservation::where('status', 'ditolak')->count();
        $cancelledCount = Reservation::where('status', 'dibatalkan')->count();
        $totalCount = Reservation::count();
        $newReportsCount = Report::where('status', 'baru')->count();

        return view('petugas.reservations.index', compact(
            'reservations',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'cancelledCount',
            'totalCount',
            'newReportsCount'
        ));
    }

    /**
     * Setujui reservasi.
     */
    public function approve(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $reservation = Reservation::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($reservation->status !== 'pending') {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Hanya reservasi pending yang bisa disetujui'], 422);
                }

                return back()->with('error', 'Hanya reservasi pending yang bisa disetujui');
            }

            // Cek bentrok jadwal dengan reservasi lain yang sudah disetujui
            $conflict = Reservation::where('facility_id', $reservation->facility_id)
                ->where('tanggal', $reservation->tanggal)
                ->where('status', 'disetujui')
                ->where('id', '!=', $reservation->id)
                ->where(function ($query) use ($reservation) {
                    $query->where('start_time', '<', $reservation->end_time)
                        ->where('end_time', '>', $reservation->start_time);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                $msg = 'Jadwal reservasi bentrok dengan reservasi lain yang sudah disetujui.';
                if ($request->wantsJson()) {
                    return response()->json(['message' => $msg], 422);
                }

                return back()->with('error', $msg);
            }

            $reservation->update([
                'status' => 'disetujui',
                'catatan_petugas' => $request->catatan_petugas ?? $reservation->catatan_petugas,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Reservasi telah disetujui.',
                    'data' => $reservation->load(['user', 'facility']),
                ]);
            }

            return back()->with('success', "Reservasi #{$reservation->id} untuk fasilitas {$reservation->facility->nama} telah disetujui.");
        });
    }

    /**
     * Tolak reservasi.
     */
    public function reject(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pending') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Hanya reservasi pending yang bisa ditolak'], 422);
            }

            return back()->with('error', 'Hanya reservasi pending yang bisa ditolak');
        }

        $request->validate([
            'catatan_petugas' => 'nullable|string|max:255',
            'alasan' => 'nullable|string|max:255',
        ]);

        $alasan = $request->alasan ?? $request->catatan_petugas ?? $reservation->catatan_petugas;

        $reservation->update([
            'status' => 'ditolak',
            'catatan_petugas' => $alasan,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Reservasi telah ditolak.',
                'data' => $reservation->load(['user', 'facility']),
            ]);
        }

        return back()->with('success', "Reservasi #{$reservation->id} telah ditolak.");
    }

    /**
     * Batalkan reservasi oleh petugas (hanya status disetujui, wajib alasan_pembatalan).
     */
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'disetujui') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Hanya reservasi yang sudah disetujui yang dapat dibatalkan petugas.'], 422);
            }

            return back()->with('error', 'Hanya reservasi yang sudah disetujui yang dapat dibatalkan petugas.');
        }

        $request->validate([
            'alasan_pembatalan' => 'required|string|max:255',
        ]);

        $reservation->update([
            'status' => 'dibatalkan',
            'catatan_petugas' => $request->alasan_pembatalan,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Reservasi berhasil dibatalkan oleh petugas.',
                'data' => $reservation->load(['user', 'facility']),
            ]);
        }

        return back()->with('success', "Reservasi #{$reservation->id} berhasil dibatalkan oleh petugas.");
    }
}
