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
                'nama' => 'Bagas Satrio Wicaksono',
                'nik' => '3516080804030002',
                'alamat' => 'Mojokerto',
                'jenis_kelamin' => 'L',
                'golongan_darah' => 'A',
                'no_telepon' => '089672635263',
                'no_bpjs' => '012345',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Benediktus Arion',
                'nik' => '3516080703040003',
                'alamat' => 'Nganjuk',
                'jenis_kelamin' => 'L',
                'golongan_darah' => 'B',
                'no_telepon' => '089746325817',
                'no_bpjs' => '067233',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Citra Anindya Putri',
                'nik' => '3516083004030001',
                'alamat' => 'Denpasar',
                'jenis_kelamin' => 'P',
                'golongan_darah' => 'O',
                'no_telepon' => '089281767832',
                'no_bpjs' => '084672',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Azkia Alesha Humaira',
                'nik' => '3516083004030002',
                'alamat' => 'Surabaya',
                'jenis_kelamin' => 'P',
                'golongan_darah' => 'AB',
                'no_telepon' => '089672635299',
                'no_bpjs' => '087649',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
