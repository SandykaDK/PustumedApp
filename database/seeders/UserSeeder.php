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
                'name' => 'User 1',
                'email' => 'sandyka@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'kepala_pustu',
                'no_telepon' => '089620106214',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User 2',
                'email' => 'ardiansyah@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'petugas_administrasi',
                'no_telepon' => '089620176453',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User 3',
                'email' => 'reno@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'petugas_obat',
                'no_telepon' => '08937361827',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('jarak123'),
                'role' => 'super_admin',
                'no_telepon' => '087362515233',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
