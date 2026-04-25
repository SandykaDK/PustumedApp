<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('satuan_obat')->insert([
            [
                'kode_satuan' => 'SAT-01',
                'satuan_obat' => 'caps',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-02',
                'satuan_obat' => 'tab',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-03',
                'satuan_obat' => 'tube',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-04',
                'satuan_obat' => 'amp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-05',
                'satuan_obat' => 'botol',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-06',
                'satuan_obat' => 'supp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-07',
                'satuan_obat' => 'bungkus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-08',
                'satuan_obat' => 'v.supp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-09',
                'satuan_obat' => 'kolf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-10',
                'satuan_obat' => 'kaplt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-11',
                'satuan_obat' => 'kaps',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-12',
                'satuan_obat' => 'pot',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-13',
                'satuan_obat' => 'ktk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-14',
                'satuan_obat' => 'kapsl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-15',
                'satuan_obat' => 'pcs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-16',
                'satuan_obat' => 'roll',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_satuan' => 'SAT-17',
                'satuan_obat' => 'pak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
