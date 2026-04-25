<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Dokter;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dokter>
 */
class DokterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_dokter' => fake()->name(),
            'spesialisasi' => fake()->randomElement(['Umum', 'Anak', 'Jantung', 'Kulit']),
            'no_telepon' => fake()->phoneNumber(),
        ];
    }
}
