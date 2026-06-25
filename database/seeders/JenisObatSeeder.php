<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table(table: 'jenis_obat')->insert([
            [
                'kode_jenis' => 'AB',
                'jenis_obat' => 'Antibiotik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'VT',
                'jenis_obat' => 'Vitamin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'AL',
                'jenis_obat' => 'Analgesik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'AP',
                'jenis_obat' => 'Antipiretik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'AV',
                'jenis_obat' => 'Antivirus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
