<?php

namespace App\Models;

use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    /** @use HasFactory<FacilityFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'tipe',
        'kapasitas',
        'lokasi',
        'status',
        'deskripsi',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function isUnderRepair(): bool
    {
        return $this->status === 'perbaikan';
    }

    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'Tersedia',
            'perbaikan' => 'Dalam Perbaikan',
            'tidak_aktif' => 'Tidak Aktif',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'perbaikan' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'tidak_aktif' => 'bg-gray-100 text-gray-700 border border-gray-200',
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };
    }
}
