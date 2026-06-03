<?php

namespace Database\Seeders;

use App\Models\DetailPenerimaanObat;
use App\Models\PenerimaanObat;
use App\Models\StokObat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenerimaanObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $penerimaanData = [
                ['no_batch' => 'BATCH-202605-001', 'tanggal_penerimaan' => '2026-05-01', 'user_id' => 1, 'keterangan' => 'Penerimaan obat rutin dari distributor bulan Mei 2026.', 'detail' => ['nama_obat_id' => 1, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-001', 'tanggal_kadaluwarsa' => '2026-06-15', 'jumlah_masuk' => 100, 'satuan_id' => 1, 'lokasi_penyimpanan' => 'Rak A1']],
                ['no_batch' => 'BATCH-202605-002', 'tanggal_penerimaan' => '2026-05-02', 'user_id' => 2, 'keterangan' => 'Penerimaan stok tambahan untuk pelayanan harian.', 'detail' => ['nama_obat_id' => 2, 'jenis_obat_id' => 5, 'no_batch' => 'BATCH-202605-002', 'tanggal_kadaluwarsa' => '2026-07-01', 'jumlah_masuk' => 120, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A2']],
                ['no_batch' => 'BATCH-202605-003', 'tanggal_penerimaan' => '2026-05-03', 'user_id' => 3, 'keterangan' => 'Penerimaan obat tambahan untuk rotasi stok.', 'detail' => ['nama_obat_id' => 3, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-003', 'tanggal_kadaluwarsa' => '2026-08-20', 'jumlah_masuk' => 90, 'satuan_id' => 3, 'lokasi_penyimpanan' => 'Rak A3']],
                ['no_batch' => 'BATCH-202605-004', 'tanggal_penerimaan' => '2026-05-04', 'user_id' => 4, 'keterangan' => 'Penerimaan persediaan obat layanan poli.', 'detail' => ['nama_obat_id' => 4, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-004', 'tanggal_kadaluwarsa' => '2027-07-15', 'jumlah_masuk' => 110, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak A4']],
                ['no_batch' => 'BATCH-202605-005', 'tanggal_penerimaan' => '2026-05-05', 'user_id' => 1, 'keterangan' => 'Penerimaan obat kategori analgesik dan vitamin.', 'detail' => ['nama_obat_id' => 5, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-005', 'tanggal_kadaluwarsa' => '2027-08-20', 'jumlah_masuk' => 95, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak B1']],
                ['no_batch' => 'BATCH-202605-006', 'tanggal_penerimaan' => '2026-05-06', 'user_id' => 2, 'keterangan' => 'Penerimaan obat untuk kebutuhan harian ruang tindakan.', 'detail' => ['nama_obat_id' => 6, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-006', 'tanggal_kadaluwarsa' => '2027-09-10', 'jumlah_masuk' => 130, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak B2']],
                ['no_batch' => 'BATCH-202605-007', 'tanggal_penerimaan' => '2026-05-07', 'user_id' => 3, 'keterangan' => 'Penerimaan obat tambahan dari distributor lokal.', 'detail' => ['nama_obat_id' => 7, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-007', 'tanggal_kadaluwarsa' => '2027-10-05', 'jumlah_masuk' => 85, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak B3']],
                ['no_batch' => 'BATCH-202605-008', 'tanggal_penerimaan' => '2026-05-08', 'user_id' => 4, 'keterangan' => 'Penerimaan stok injeksi untuk pelayanan medis.', 'detail' => ['nama_obat_id' => 8, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-008', 'tanggal_kadaluwarsa' => '2027-11-12', 'jumlah_masuk' => 70, 'satuan_id' => 4, 'lokasi_penyimpanan' => 'Rak B4']],
                ['no_batch' => 'BATCH-202605-009', 'tanggal_penerimaan' => '2026-05-09', 'user_id' => 1, 'keterangan' => 'Penerimaan obat untuk kebutuhan pasien rutin.', 'detail' => ['nama_obat_id' => 9, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-009', 'tanggal_kadaluwarsa' => '2027-12-01', 'jumlah_masuk' => 140, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak C1']],
                ['no_batch' => 'BATCH-202605-010', 'tanggal_penerimaan' => '2026-05-10', 'user_id' => 2, 'keterangan' => 'Penerimaan antibiotik untuk pelayanan farmasi.', 'detail' => ['nama_obat_id' => 10, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-010', 'tanggal_kadaluwarsa' => '2028-01-10', 'jumlah_masuk' => 100, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak C2']],
                ['no_batch' => 'BATCH-202605-011', 'tanggal_penerimaan' => '2026-05-11', 'user_id' => 3, 'keterangan' => 'Penerimaan sediaan sirup untuk pasien anak.', 'detail' => ['nama_obat_id' => 11, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-011', 'tanggal_kadaluwarsa' => '2028-02-14', 'jumlah_masuk' => 60, 'satuan_id' => 5, 'lokasi_penyimpanan' => 'Rak C3']],
                ['no_batch' => 'BATCH-202605-012', 'tanggal_penerimaan' => '2026-05-12', 'user_id' => 4, 'keterangan' => 'Penerimaan stok tablet untuk gudang obat.', 'detail' => ['nama_obat_id' => 12, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-012', 'tanggal_kadaluwarsa' => '2028-03-01', 'jumlah_masuk' => 125, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak C4']],
                ['no_batch' => 'BATCH-202605-013', 'tanggal_penerimaan' => '2026-05-13', 'user_id' => 1, 'keterangan' => 'Penerimaan obat cair untuk stok pelayanan.', 'detail' => ['nama_obat_id' => 13, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-013', 'tanggal_kadaluwarsa' => '2028-04-17', 'jumlah_masuk' => 75, 'satuan_id' => 5, 'lokasi_penyimpanan' => 'Rak D1']],
                ['no_batch' => 'BATCH-202605-014', 'tanggal_penerimaan' => '2026-05-14', 'user_id' => 2, 'keterangan' => 'Penerimaan obat topikal untuk poli umum.', 'detail' => ['nama_obat_id' => 14, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-014', 'tanggal_kadaluwarsa' => '2028-05-21', 'jumlah_masuk' => 65, 'satuan_id' => 3, 'lokasi_penyimpanan' => 'Rak D2']],
                ['no_batch' => 'BATCH-202605-015', 'tanggal_penerimaan' => '2026-05-15', 'user_id' => 3, 'keterangan' => 'Penerimaan stok penutup periode pertengahan bulan.', 'detail' => ['nama_obat_id' => 15, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-015', 'tanggal_kadaluwarsa' => '2028-06-30', 'jumlah_masuk' => 90, 'satuan_id' => 15, 'lokasi_penyimpanan' => 'Rak D3']],
                ['no_batch' => 'BATCH-202605-016', 'tanggal_penerimaan' => '2026-05-16', 'user_id' => 4, 'keterangan' => 'Penerimaan obat suppositoria untuk stok farmasi prioritas.', 'detail' => ['nama_obat_id' => 16, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-016', 'tanggal_kadaluwarsa' => '2026-06-05', 'jumlah_masuk' => 50, 'satuan_id' => 6, 'lokasi_penyimpanan' => 'Rak E1']],
                ['no_batch' => 'BATCH-202605-017', 'tanggal_penerimaan' => '2026-05-17', 'user_id' => 1, 'keterangan' => 'Penerimaan stok analgesik yang perlu diprioritaskan.', 'detail' => ['nama_obat_id' => 17, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-017', 'tanggal_kadaluwarsa' => '2026-06-10', 'jumlah_masuk' => 80, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak E2']],
                ['no_batch' => 'BATCH-202605-018', 'tanggal_penerimaan' => '2026-05-18', 'user_id' => 2, 'keterangan' => 'Penerimaan vitamin C untuk kebutuhan cepat edar.', 'detail' => ['nama_obat_id' => 18, 'jenis_obat_id' => 1, 'no_batch' => 'BATCH-202605-018', 'tanggal_kadaluwarsa' => '2026-06-18', 'jumlah_masuk' => 60, 'satuan_id' => 2, 'lokasi_penyimpanan' => 'Rak E3']],
            ];

            foreach ($penerimaanData as $data) {
                $detail = $data['detail'];
                unset($data['detail']);

                $penerimaan = PenerimaanObat::create($data);

                DetailPenerimaanObat::create([
                    'penerimaan_obat_id' => $penerimaan->id,
                    ...$detail,
                ]);

                StokObat::create([
                    'nama_obat_id' => $detail['nama_obat_id'],
                    'tanggal_kadaluwarsa' => $detail['tanggal_kadaluwarsa'],
                    'stok' => $detail['jumlah_masuk'],
                    'no_batch' => $detail['no_batch'],
                    'keterangan' => 'Stok awal dari penerimaan obat seeder.',
                ]);
            }
        });
    }
}
