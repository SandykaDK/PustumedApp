<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PasienSeeder::class,
            DokterSeeder::class,
            JenisObatSeeder::class,
            SatuanObatSeeder::class,
            NamaObatSeeder::class,
            // PenerimaanObatSeeder::class,
            // PengeluaranObatSeeder::class,
            PenerimaanObatSeeder2::class,
            PengeluaranObatSeeder2::class,
        ]);
    }
}
