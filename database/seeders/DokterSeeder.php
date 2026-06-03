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
                'jenis_kelamin' => 'L',
                'no_telepon' => '089672635263',
                'email' => 'budi@gmail.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Andre',
                'alamat' => 'Sidoarjo',
                'jenis_kelamin' => 'L',
                'no_telepon' => '0896273463527',
                'email' => 'andre@gmail.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Michie',
                'alamat' => 'Mojokerto',
                'jenis_kelamin' => 'P',
                'no_telepon' => '089726273635',
                'email' => 'michie@gmail.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
