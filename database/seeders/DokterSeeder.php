<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokterSeeder extends Seeder
{
public function run(): void
    {
        DB::table('dokter')->insert([
            [
                'nama' => 'Dr. Budi',
                'alamat' => 'Mojokerto',
                'no_telepon' => '089672635263',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Andre',
                'alamat' => 'Sidoarjo',
                'no_telepon' => '0896273463527',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Michie',
                'alamat' => 'Mojokerto',
                'no_telepon' => '089726273635',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
