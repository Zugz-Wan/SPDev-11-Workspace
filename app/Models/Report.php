<?php

namespace App\Models;

use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'kategori',
        'deskripsi',
        'foto',
        'status',
        'catatan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'diproses' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'selesai' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'ditolak' => 'bg-rose-100 text-rose-800 border border-rose-200',
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };
    }
}
