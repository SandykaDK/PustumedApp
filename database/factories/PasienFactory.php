<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pasien;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pasien>
 */
class PasienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_pasien' => fake()->name(),
            'no_bpjs' => fake()->unique()->numerify('###############'),
            'alamat' => fake()->address(),
            'no_telepon' => fake()->phoneNumber(),
        ];
    }
}
