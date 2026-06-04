<?php

namespace Database\Seeders;

use App\Models\DetailPengeluaranObat;
use App\Models\PengeluaranObat;
use App\Models\StokObat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $batchNos = [
                'BATCH-202605-001',
                'BATCH-202605-002',
                'BATCH-202605-003',
                'BATCH-202605-004',
                'BATCH-202605-005',
                'BATCH-202605-006',
                'BATCH-202605-007',
                'BATCH-202605-008',
                'BATCH-202605-009',
                'BATCH-202605-010',
                'BATCH-202605-011',
                'BATCH-202605-012',
                'BATCH-202605-013',
                'BATCH-202605-014',
                'BATCH-202605-015',
            ];

            $stokByBatch = StokObat::whereIn('no_batch', $batchNos)->get()->keyBy('no_batch');

            $userIds = [1, 2, 3, 4];
            $pasienIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
            $dokterIds = [1, 2, 3];
            $lokasiPenyimpanan = ['Rak A1', 'Rak A2', 'Rak A3', 'Rak A4', 'Rak B1', 'Rak B2', 'Rak B3', 'Rak B4', 'Rak C1', 'Rak C2', 'Rak C3', 'Rak C4', 'Rak D1', 'Rak D2', 'Rak D3'];

            $pengeluaranData = [];

            for ($i = 0; $i < 56; $i++) {
                $batchNo = $batchNos[$i % count($batchNos)];
                $stok = $stokByBatch[$batchNo];

                $pengeluaranData[] = [
                    'tanggal_pengeluaran' => Carbon::create(2026, 5, 10)->addDays($i)->format('Y-m-d'),
                    'user_id' => $userIds[$i % count($userIds)],
                    'pasien_id' => $pasienIds[$i % count($pasienIds)],
                    'dokter_id' => $dokterIds[$i % count($dokterIds)],
                    'keterangan' => 'Pengeluaran obat seeder ke-' . ($i + 1) . ' untuk mendukung data tiap user.',
                    'detail' => [
                        'nama_obat_id' => $stok->nama_obat_id,
                        'jumlah_keluar' => 4 + ($i % 12),
                        'satuan_id' => ($i % 5) + 1,
                        'stok_obat_id' => $stok->id,
                        'lokasi_penyimpanan' => $lokasiPenyimpanan[$i % count($lokasiPenyimpanan)],
                    ],
                ];
            }

            foreach ($pengeluaranData as $data) {
                $detail = $data['detail'];
                unset($data['detail']);

                $pengeluaran = PengeluaranObat::create($data);

                DetailPengeluaranObat::create([
                    'pengeluaran_obat_id' => $pengeluaran->id,
                    ...$detail,
                ]);
            }
        });
    }
}
