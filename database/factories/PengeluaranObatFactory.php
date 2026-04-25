<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PengeluaranObat;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PengeluaranObat>
 */
class PengeluaranObatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tanggal_pengeluaran' => fake()->date(),
            'pasien_id' => \App\Models\Pasien::factory(),
            'dokter_id' => \App\Models\Dokter::factory(),
            'user_id' => \App\Models\User::factory(),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }
}
