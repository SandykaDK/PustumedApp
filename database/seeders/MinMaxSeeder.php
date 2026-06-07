<?php

namespace Database\Seeders;

use App\Services\MinMaxService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinMaxSeeder extends Seeder
{
    public function run(): void
    {
        $service = new MinMaxService();

        $records = DB::table('detail_pengeluaran_obat')
            ->join('pengeluaran_obat', 'detail_pengeluaran_obat.pengeluaran_obat_id', '=', 'pengeluaran_obat.id')
            ->select(
                'detail_pengeluaran_obat.nama_obat_id',
                DB::raw('YEAR(pengeluaran_obat.tanggal_pengeluaran) as periode_year'),
                DB::raw('MONTH(pengeluaran_obat.tanggal_pengeluaran) as periode_month')
            )
            ->distinct()
            ->get();

        foreach ($records as $record) {
            $recordDate = sprintf('%04d-%02d-01', $record->periode_year, $record->periode_month);
            $service->calculateAndUpdate($record->nama_obat_id, $recordDate);
        }
    }
}
