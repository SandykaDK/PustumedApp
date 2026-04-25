<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pasien')->insert([
            [
                'nama' => 'Ardiansyah',
                'nik' => '3516080804030002',
                'alamat' => 'Mojokerto',
                'no_telepon' => '089672635263',
                'no_bpjs' => '012345',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Reno',
                'nik' => '3516080703040003',
                'alamat' => 'Nganjuk',
                'no_telepon' => '089746325817',
                'no_bpjs' => '067233',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Rommy',
                'nik' => '3516083004030001',
                'alamat' => 'Denpasar',
                'no_telepon' => '089281767832',
                'no_bpjs' => '084672',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
