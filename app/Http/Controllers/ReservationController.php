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
            ->latest('tanggal')
            ->latest('start_time')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($reservations);
        }

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Tampilkan detail lengkap reservasi.
     */
    public function show(Request $request, $id)
    {
        $reservation = Reservation::with(['facility', 'user'])->findOrFail($id);

        if ($reservation->user_id !== auth()->id() && ! auth()->user()->isPetugas()) {
            abort(403, 'Akses tidak diizinkan untuk melihat reservasi ini.');
        }

        if ($request->wantsJson()) {
            return response()->json($reservation);
        }

        return view('reservations.show', compact('reservation'));
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

        if ($request->wantsJson()) {
            return response()->json($reservation, 201);
        }

        return redirect()->route('reservations.my')->with('success', 'Pengajuan reservasi berhasil dikirim dan menunggu persetujuan petugas.');
    }

    /**
     * Pembatalan reservasi oleh pengguna.
     */
    public function destroy(Request $request, $id)
    {
        $reservation = Reservation::where('user_id', auth()->id())->findOrFail($id);

        if (! in_array($reservation->status, ['pending', 'disetujui'], true)) {
            $message = 'Hanya reservasi berstatus pending atau disetujui yang dapat dibatalkan.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if (! $reservation->canBeCancelled()) {
            $hours = (int) config('app.reservation_cancellation_hours', Reservation::CANCELLATION_MIN_HOURS_BEFORE);
            $message = "Reservasi tidak dapat dibatalkan karena waktu mulai kurang dari {$hours} jam dari sekarang atau sudah lewat.";
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $reservation->update([
            'status' => 'dibatalkan',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Reservasi berhasil dibatalkan.',
                'data' => $reservation,
            ]);
        }

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
