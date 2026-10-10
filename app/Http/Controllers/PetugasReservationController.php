<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasReservationController extends Controller
{
    /**
     * Tampilkan antrian reservasi untuk petugas & admin.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Reservation::with(['user', 'facility'])->latest();

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('facility', function ($fq) use ($search) {
                    $fq->where('nama', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhere('keperluan', 'like', "%{$search}%");
            });
        }

        $reservations = $query->paginate(15)->withQueryString();

        if ($request->expectsJson() && ! $request->hasHeader('X-Inertia') && ! $request->acceptsHtml()) {
            return response()->json($request->has('status') ? $query->get() : Reservation::with(['user', 'facility'])->latest()->get());
        }

        $pendingCount = Reservation::where('status', 'pending')->count();
        $approvedCount = Reservation::where('status', 'disetujui')->count();

        return view('officer.reservations.index', compact('reservations', 'status', 'search', 'pendingCount', 'approvedCount'));
    }

    /**
     * Setujui reservasi dan cegah bentrok jadwal.
     */
    public function approve(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $reservation = Reservation::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($reservation->status !== 'pending') {
                if ($request->expectsJson() && ! $request->acceptsHtml()) {
                    return response()->json(['message' => 'Hanya reservasi pending yang bisa disetujui'], 422);
                }

                return back()->with('error', 'Hanya reservasi pending yang bisa disetujui');
            }

            // Validasi bentrok jadwal
            $conflict = Reservation::where('facility_id', $reservation->facility_id)
                ->where('tanggal', $reservation->tanggal)
                ->where('status', 'disetujui')
                ->where('id', '!=', $reservation->id)
                ->where(function ($q) use ($reservation) {
                    $q->where('start_time', '<', $reservation->end_time)
                        ->where('end_time', '>', $reservation->start_time);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                if ($request->expectsJson() && ! $request->acceptsHtml()) {
                    return response()->json([
                        'message' => 'Gagal menyetujui: Jadwal bentrok dengan reservasi lain yang sudah disetujui pada fasilitas yang sama.',
                    ], 422);
                }

                return back()->with('error', 'Gagal menyetujui: Jadwal bentrok dengan reservasi lain yang sudah disetujui pada fasilitas dan jam yang sama.');
            }

            $reservation->update([
                'status' => 'disetujui',
                'catatan_petugas' => $request->catatan_petugas ?? $reservation->catatan_petugas,
            ]);

            if ($request->expectsJson() && ! $request->acceptsHtml()) {
                return response()->json([
                    'message' => 'Reservasi telah disetujui.',
                    'data' => $reservation->load(['user', 'facility']),
                ]);
            }

            return back()->with('success', "Reservasi #{$reservation->id} untuk fasilitas '{$reservation->facility->nama}' berhasil disetujui.");
        });
    }

    /**
     * Tolak permohonan reservasi.
     */
    public function reject(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pending') {
            if ($request->expectsJson() && ! $request->acceptsHtml()) {
                return response()->json(['message' => 'Hanya reservasi pending yang bisa ditolak'], 422);
            }

            return back()->with('error', 'Hanya reservasi pending yang bisa ditolak');
        }

        $request->validate([
            'catatan_petugas' => 'nullable|string|max:255',
            'alasan' => 'nullable|string|max:255',
        ]);

        $alasan = $request->alasan ?? $request->catatan_petugas ?? 'Tidak memenuhi syarat penggunaan fasilitas.';

        $reservation->update([
            'status' => 'ditolak',
            'catatan_petugas' => $alasan,
        ]);

        if ($request->expectsJson() && ! $request->acceptsHtml()) {
            return response()->json([
                'message' => 'Reservasi telah ditolak.',
                'data' => $reservation->load(['user', 'facility']),
            ]);
        }

        return back()->with('success', "Reservasi #{$reservation->id} telah ditolak.");
    }

    /**
     * Batalkan reservasi yang sudah disetujui dalam kondisi mendesak dengan alasan.
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan_pembatalan' => 'required_without:catatan_petugas|nullable|string|max:255',
            'catatan_petugas' => 'required_without:alasan_pembatalan|nullable|string|max:255',
        ]);

        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'disetujui') {
            if ($request->expectsJson() && ! $request->acceptsHtml()) {
                return response()->json(['message' => 'Hanya reservasi yang sudah disetujui yang dapat dibatalkan petugas.'], 422);
            }

            return back()->with('error', 'Hanya reservasi yang sudah disetujui yang dapat dibatalkan petugas.');
        }

        $alasan = $request->alasan_pembatalan ?? $request->catatan_petugas;

        $reservation->update([
            'status' => 'dibatalkan',
            'catatan_petugas' => $alasan,
        ]);

        if ($request->expectsJson() && ! $request->acceptsHtml()) {
            return response()->json([
                'message' => 'Reservasi berhasil dibatalkan oleh petugas.',
                'data' => $reservation->load(['user', 'facility']),
            ]);
        }

        return back()->with('success', "Reservasi #{$reservation->id} telah dibatalkan dengan alasan: {$alasan}");
    }
}
