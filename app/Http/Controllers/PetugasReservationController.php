<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class PetugasReservationController extends Controller
{
    /**
     * Tampilkan daftar reservasi untuk petugas.
     * Bisa difilter berdasarkan status (default: pending).
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'facility']);

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $reservations = $query->latest()->get();

        return response()->json($reservations);
    }

    /**
     * Setujui reservasi.
     */
    public function approve(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status' => 'disetujui',
            'catatan_petugas' => $request->catatan_petugas ?? $reservation->catatan_petugas,
        ]);

        return response()->json([
            'message' => 'Reservasi telah disetujui.',
            'data' => $reservation->load(['user', 'facility']),
        ]);
    }

    /**
     * Tolak reservasi.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_petugas' => 'nullable|string|max:255',
            'alasan' => 'nullable|string|max:255',
        ]);

        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status' => 'ditolak',
            'catatan_petugas' => $request->alasan ?? $request->catatan_petugas ?? $reservation->catatan_petugas,
        ]);

        return response()->json([
            'message' => 'Reservasi telah ditolak.',
            'data' => $reservation->load(['user', 'facility']),
        ]);
    }

    /**
     * Batalkan reservasi oleh petugas (membutuhkan alasan_pembatalan / catatan_petugas).
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan_pembatalan' => 'required_without:catatan_petugas|nullable|string|max:255',
            'catatan_petugas' => 'required_without:alasan_pembatalan|nullable|string|max:255',
        ]);

        $reservation = Reservation::findOrFail($id);

        $alasan = $request->alasan_pembatalan ?? $request->catatan_petugas;

        $reservation->update([
            'status' => 'dibatalkan',
            'catatan_petugas' => $alasan,
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibatalkan oleh petugas.',
            'data' => $reservation->load(['user', 'facility']),
        ]);
    }
}
