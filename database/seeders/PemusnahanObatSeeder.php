<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemusnahanObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk Pemusnahan dengan status PENDING
        $pendinPemusnahan = [
            [
                'user_id' => 3, // User 3 (petugas_obat)
                'tanggal_pengajuan' => Carbon::now()->subDays(5),
                'tanggal_pemusnahan' => null,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'keterangan' => 'Pengajuan pemusnahan obat kadaluwarsa batch pertama',
                'bukti_foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'tanggal_pengajuan' => Carbon::now()->subDays(3),
                'tanggal_pemusnahan' => null,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'keterangan' => 'Pemusnahan obat rusak dan tidak layak pakai',
                'bukti_foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'tanggal_pengajuan' => Carbon::now()->subDays(1),
                'tanggal_pemusnahan' => null,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'keterangan' => 'Obat dengan kemasan yang sudah terbuka dan tercemar',
                'bukti_foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Data untuk Pemusnahan dengan status APPROVED
        $approvedPemusnahan = [
            [
                'user_id' => 3,
                'tanggal_pengajuan' => Carbon::now()->subDays(30),
                'tanggal_pemusnahan' => Carbon::now()->subDays(28),
                'status' => 'approved',
                'approved_by' => 1, // User 1 (kepala_pustu)
                'approved_at' => Carbon::now()->subDays(29),
                'keterangan' => 'Pemusnahan obat kadaluwarsa batch januari 2025',
                'bukti_foto' => null,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'user_id' => 3,
                'tanggal_pengajuan' => Carbon::now()->subDays(20),
                'tanggal_pemusnahan' => Carbon::now()->subDays(18),
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(19),
                'keterangan' => 'Obat dengan kualitas menurun dan tidak memenuhi standar',
                'bukti_foto' => null,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'user_id' => 3,
                'tanggal_pengajuan' => Carbon::now()->subDays(10),
                'tanggal_pemusnahan' => Carbon::now()->subDays(8),
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(9),
                'keterangan' => 'Pemusnahan stok obat yang berlebihan dan sudah expired',
                'bukti_foto' => null,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(8),
            ],
        ];

        // Insert Pending Pemusnahan
        $pendingIds = [];
        foreach ($pendinPemusnahan as $data) {
            $pendingIds[] = DB::table('pemusnahan_obat')->insertGetId($data);
        }

        // Insert Approved Pemusnahan
        $approvedIds = [];
        foreach ($approvedPemusnahan as $data) {
            $approvedIds[] = DB::table('pemusnahan_obat')->insertGetId($data);
        }

        // Detail untuk Pending Pemusnahan
        $pendingDetails = [
            // Pending 1 - 2 items
            [
                'pemusnahan_obat_id' => $pendingIds[0],
                'nama_obat_id' => 1, // Acethylsisteine capsule
                'stok_obat_id' => null,
                'jumlah' => 50,
                'satuan_id' => 1,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pemusnahan_obat_id' => $pendingIds[0],
                'nama_obat_id' => 5, // Allopurinol 100 mg
                'stok_obat_id' => null,
                'jumlah' => 30,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Pending 2 - 3 items
            [
                'pemusnahan_obat_id' => $pendingIds[1],
                'nama_obat_id' => 10, // Amoksisilin 500 mg
                'stok_obat_id' => null,
                'jumlah' => 100,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pemusnahan_obat_id' => $pendingIds[1],
                'nama_obat_id' => 15, // Anti Fungi Salep
                'stok_obat_id' => null,
                'jumlah' => 25,
                'satuan_id' => 3,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pemusnahan_obat_id' => $pendingIds[1],
                'nama_obat_id' => 20, // Betametason krim 0,1%
                'stok_obat_id' => null,
                'jumlah' => 15,
                'satuan_id' => 3,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Pending 3 - 2 items
            [
                'pemusnahan_obat_id' => $pendingIds[2],
                'nama_obat_id' => 22, // Deksametason Tablet 0,5 mg
                'stok_obat_id' => null,
                'jumlah' => 60,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pemusnahan_obat_id' => $pendingIds[2],
                'nama_obat_id' => 32, // Furosemid Tablet 40 mg
                'stok_obat_id' => null,
                'jumlah' => 40,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Detail untuk Approved Pemusnahan
        $approvedDetails = [
            // Approved 1 - 2 items
            [
                'pemusnahan_obat_id' => $approvedIds[0],
                'nama_obat_id' => 2, // Acyclovir 400 mg
                'stok_obat_id' => null,
                'jumlah' => 75,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'pemusnahan_obat_id' => $approvedIds[0],
                'nama_obat_id' => 6, // Ambroxol Tab
                'stok_obat_id' => null,
                'jumlah' => 90,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            // Approved 2 - 3 items
            [
                'pemusnahan_obat_id' => $approvedIds[1],
                'nama_obat_id' => 3, // Acyclovir Cream
                'stok_obat_id' => null,
                'jumlah' => 35,
                'satuan_id' => 3,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'pemusnahan_obat_id' => $approvedIds[1],
                'nama_obat_id' => 9, // Amlodipin 5 mg / 10 mg
                'stok_obat_id' => null,
                'jumlah' => 55,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'pemusnahan_obat_id' => $approvedIds[1],
                'nama_obat_id' => 25, // Dimenhidrinat 50 mg
                'stok_obat_id' => null,
                'jumlah' => 42,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            // Approved 3 - 3 items
            [
                'pemusnahan_obat_id' => $approvedIds[2],
                'nama_obat_id' => 4, // Albendazole Tab
                'stok_obat_id' => null,
                'jumlah' => 80,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'pemusnahan_obat_id' => $approvedIds[2],
                'nama_obat_id' => 11, // Amoksisilin sirup kering 125 mg/5 ml
                'stok_obat_id' => null,
                'jumlah' => 20,
                'satuan_id' => 5,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'pemusnahan_obat_id' => $approvedIds[2],
                'nama_obat_id' => 40, // Ibuprofen tablet 200mg / 400 mg
                'stok_obat_id' => null,
                'jumlah' => 120,
                'satuan_id' => 2,
                'lokasi_penyimpanan' => 'Rak A1',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(8),
            ],
        ];

        // Insert all details
        DB::table('detail_pemusnahan_obat')->insert(array_merge($pendingDetails, $approvedDetails));
    }
}
