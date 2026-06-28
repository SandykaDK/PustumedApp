<?php

namespace Database\Seeders;

use App\Models\DetailPenerimaanObat;
use App\Models\PenerimaanObat;
use App\Models\StokObat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenerimaanObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $penerimaanData = [
                ['no_batch' => '202606-001', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 1, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Acethylsisteine capsule.', 'detail' => ['nama_obat_id' => 1, 'jenis_obat_id' => 1, 'no_batch' => '202606-001', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 1, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-002', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 2, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Acyclovir 400 mg.', 'detail' => ['nama_obat_id' => 2, 'jenis_obat_id' => 1, 'no_batch' => '202606-002', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-003', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 3, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Acyclovir Cream.', 'detail' => ['nama_obat_id' => 3, 'jenis_obat_id' => 1, 'no_batch' => '202606-003', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 3, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-004', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 4, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Albendazole Tab.', 'detail' => ['nama_obat_id' => 4, 'jenis_obat_id' => 1, 'no_batch' => '202606-004', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-005', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 1, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Allopurinol 100 mg.', 'detail' => ['nama_obat_id' => 5, 'jenis_obat_id' => 1, 'no_batch' => '202606-005', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-006', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 2, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Ambroxol Tab.', 'detail' => ['nama_obat_id' => 6, 'jenis_obat_id' => 1, 'no_batch' => '202606-006', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-007', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 3, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Aminophylline 200 mg.', 'detail' => ['nama_obat_id' => 7, 'jenis_obat_id' => 1, 'no_batch' => '202606-007', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-008', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 4, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Aminophylline injeksi.', 'detail' => ['nama_obat_id' => 8, 'jenis_obat_id' => 1, 'no_batch' => '202606-008', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 4, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-009', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 1, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Amlodipin 5 mg / 10 mg.', 'detail' => ['nama_obat_id' => 9, 'jenis_obat_id' => 1, 'no_batch' => '202606-009', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => '202606-010', 'tanggal_penerimaan' => '2026-06-01', 'user_id' => 2, 'keterangan' => 'Penerimaan stok awal Juni 2026 untuk Amoksisilin 500 mg.', 'detail' => ['nama_obat_id' => 10, 'jenis_obat_id' => 1, 'no_batch' => '202606-010', 'tanggal_kadaluwarsa' => '2027-06-01', 'jumlah_masuk' => 500, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A1']],
            ];

            foreach ($penerimaanData as $data) {
                $detail = $data['detail'];
                unset($data['detail']);

                $penerimaan = PenerimaanObat::create($data);

                $detailBatch = $penerimaan->no_batch . '-' . Carbon::parse($detail['tanggal_kadaluwarsa'])->format('Ymd');

                DetailPenerimaanObat::create([
                    'penerimaan_obat_id' => $penerimaan->id,
                    ...$detail,
                    'no_batch' => $detailBatch,
                ]);

                StokObat::create([
                    'nama_obat_id' => $detail['nama_obat_id'],
                    'tanggal_kadaluwarsa' => $detail['tanggal_kadaluwarsa'],
                    'stok' => $detail['jumlah_masuk'],
                    'no_batch' => $detailBatch,
                    'keterangan' => 'Stok awal dari penerimaan obat seeder.',
                ]);
            }
        });
    }
}
