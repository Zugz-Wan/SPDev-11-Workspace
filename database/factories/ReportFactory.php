<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'facility_id' => Facility::factory(),
            'kategori' => fake()->randomElement(['Kerusakan Fisik', 'Kelistrikan', 'Kebersihan', 'Keamanan', 'Lainnya']),
            'deskripsi' => fake()->paragraph(),
            'foto' => null,
            'status' => 'baru',
            'catatan' => null,
        ];
    }

    public function diproses(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'diproses',
            'catatan' => 'Laporan sedang ditindaklanjuti oleh petugas.',
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
            'catatan' => 'Perbaikan fasilitas telah selesai dilaksanakan.',
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
            'catatan' => 'Laporan tidak dapat diproses karena data tidak valid.',
        ]);
    }
}
