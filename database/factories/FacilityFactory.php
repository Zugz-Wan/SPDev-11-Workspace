<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->unique()->words(2, true).' '.fake()->randomElement(['Room', 'Hall', 'Lab', 'Field']),
            'tipe' => fake()->randomElement(['ruangan', 'lapangan', 'aula', 'laboratorium']),
            'kapasitas' => fake()->numberBetween(10, 200),
            'lokasi' => 'Gedung '.fake()->randomElement(['A', 'B', 'C', 'D']).' Lantai '.fake()->numberBetween(1, 5),
            'status' => 'tersedia',
            'deskripsi' => fake()->sentence(8),
        ];
    }

    public function perbaikan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'perbaikan',
        ]);
    }

    public function tidakAktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'tidak_aktif',
        ]);
    }
}
