<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Tampilkan riwayat reservasi milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::with('facility')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json($reservations);
    }

    /**
     * Simpan reservasi baru.
     */
    public function store(StoreReservationRequest $request)
    {
        $keperluan = $request->keperluan ?? $request->tujuan_penggunaan;

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'facility_id' => $request->facility_id,
            'tanggal' => $request->tanggal,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'keperluan' => $keperluan,
            'status' => 'pending',
        ]);

        return response()->json($reservation, 201);
    }

    /**
     * Pembatalan reservasi oleh pengguna.
     */
    public function destroy($id)
    {
        $reservation = Reservation::where('user_id', auth()->id())->findOrFail($id);

        $reservation->update([
            'status' => 'dibatalkan',
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibatalkan.',
            'data' => $reservation,
        ]);
    }
}

