<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id', 'facility_id', 'tanggal', 'start_time',
        'end_time', 'keperluan', 'status', 'catatan_petugas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public const CANCELLATION_MIN_HOURS_BEFORE = 2;

    public function canBeCancelled(): bool
    {
        if (! in_array($this->status, ['pending', 'disetujui'], true)) {
            return false;
        }

        $hours = (int) config('app.reservation_cancellation_hours', self::CANCELLATION_MIN_HOURS_BEFORE);
        $startDateTime = Carbon::parse($this->tanggal.' '.$this->start_time);

        return now()->addHours($hours)->lt($startDateTime);
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'disetujui' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'ditolak' => 'bg-rose-50 text-rose-700 border border-rose-200',
            'dibatalkan' => 'bg-slate-100 text-slate-600 border border-slate-200',
            default => 'bg-slate-100 text-slate-600 border border-slate-200',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'disetujui' => 'Disetujui',
            'pending' => 'Menunggu Konfirmasi',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
