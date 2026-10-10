<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Tampilkan riwayat dan status reservasi milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::with('facility')
            ->where('user_id', auth()->id())
            ->latest('tanggal')
            ->latest('start_time')
            ->get();

        if ($request->expectsJson() && ! $request->hasHeader('X-Inertia') && ! $request->acceptsHtml()) {
            return response()->json($reservations);
        }

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Simpan pengajuan reservasi baru.
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

        if ($request->expectsJson() && ! $request->acceptsHtml()) {
            return response()->json($reservation, 201);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Pengajuan reservasi berhasil dikirim! Menunggu persetujuan dari Petugas Fasilitas.');
    }

    /**
     * Pembatalan reservasi mandiri oleh pengguna.
     */
    public function destroy(Request $request, $id)
    {
        $reservation = Reservation::where('user_id', auth()->id())->findOrFail($id);

        if ($reservation->status === 'dibatalkan' || $reservation->status === 'ditolak') {
            if ($request->expectsJson() && ! $request->acceptsHtml()) {
                return response()->json(['message' => 'Reservasi sudah tidak aktif.'], 400);
            }

            return back()->with('error', 'Reservasi ini sudah berstatus '.$reservation->status);
        }

        $reservation->update([
            'status' => 'dibatalkan',
        ]);

        if ($request->expectsJson() && ! $request->acceptsHtml()) {
            return response()->json([
                'message' => 'Reservasi berhasil dibatalkan.',
                'data' => $reservation,
            ]);
        }

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
