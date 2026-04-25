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
                'kode_jenis' => 'J-001',
                'jenis_obat' => 'Antibiotik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'J-002',
                'jenis_obat' => 'Vitamin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'J-003',
                'jenis_obat' => 'Analgesik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'J-004',
                'jenis_obat' => 'Antipiretik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jenis' => 'J-005',
                'jenis_obat' => 'Antivirus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
