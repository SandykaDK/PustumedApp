<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Sandyka Dwi Kurniawan',
                'email' => 'sandyka@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'kepala_pustu',
                'no_telepon' => '089620106214',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ardiansyah Ryanova Ashari',
                'email' => 'ardiansyah@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'petugas_administrasi',
                'no_telepon' => '089620176453',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Reno Putro Setyo Singgih',
                'email' => 'reno@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'petugas_obat',
                'no_telepon' => '08937361827',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
