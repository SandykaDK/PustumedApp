<?php

namespace Database\Seeders;

use App\Models\DetailPengeluaranObat;
use App\Models\Dokter;
use App\Models\NamaObat;
use App\Models\Pasien;
use App\Models\PengeluaranObat;
use App\Models\SatuanObat;
use App\Models\StokObat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $monthlyUsage = [
                'Acethylsisteine capsule' => [9, 14, 5, 7, 5, 8, 5, 14, 14, 11, 5, 15, 11, 2, 4, 8, 9, 9, 13, 9, 1, 2, 14, 9, 6, 2, 11, 1, 14, 7],
                'Acyclovir 400 mg' => [15, 11, 4, 14, 14, 9, 10, 5, 5, 14, 9, 11, 1, 1, 9, 3, 15, 10, 15, 11, 4, 8, 10, 8, 5, 3, 4, 9, 10, 2],
                'Acyclovir Cream' => [5, 11, 9, 14, 7, 1, 11, 13, 4, 12, 2, 3, 11, 1, 9, 1, 6, 2, 11, 12, 7, 10, 2, 10, 13, 7, 10, 13, 7, 10],
                'Albendazole Tab' => [11, 8, 6, 10, 4, 13, 2, 3, 9, 10, 4, 13, 1, 2, 3, 1, 4, 3, 3, 13, 10, 4, 9, 13, 11, 1, 4, 8, 15, 13],
                'Allopurinol 100 mg' => [8, 2, 15, 4, 13, 10, 14, 6, 3, 11, 8, 11, 6, 15, 10, 2, 12, 9, 6, 5, 2, 3, 4, 11, 10, 12, 2, 14, 8, 9],
                'Ambroxol Tab' => [12, 3, 14, 14, 7, 15, 15, 6, 12, 5, 8, 15, 1, 6, 8, 2, 7, 8, 2, 7, 10, 2, 11, 6, 5, 10, 5, 5, 8, 13],
                'Aminophylline 200 mg' => [3, 4, 15, 1, 15, 10, 4, 5, 2, 11, 9, 3, 7, 13, 2, 11, 5, 15, 5, 2, 8, 12, 13, 1, 10, 5, 12, 12, 10, 11],
                'Aminophylline injeksi' => [9, 12, 2, 8, 14, 11, 5, 1, 12, 3, 6, 4, 15, 1, 3, 12, 15, 11, 4, 13, 2, 14, 15, 3, 13, 7, 13, 4, 10, 1],
                'Amlodipin 5 mg / 10 mg' => [15, 14, 7, 1, 11, 5, 1, 8, 4, 11, 9, 13, 7, 13, 7, 2, 5, 12, 12, 7, 2, 14, 9, 10, 3, 11, 5, 7, 12, 8],
                'Amoksisilin 500 mg' => [15, 1, 4, 6, 6, 15, 15, 3, 1, 3, 3, 13, 11, 9, 15, 9, 6, 10, 3, 2, 5, 1, 2, 10, 11, 3, 2, 10, 1, 2],
            ];

            $namaObatMap = NamaObat::whereIn('nama_obat', array_keys($monthlyUsage))
                ->get()
                ->keyBy('nama_obat');

            $stokByNamaObatId = StokObat::whereIn('nama_obat_id', $namaObatMap->pluck('id'))
                ->get()
                ->groupBy('nama_obat_id');

            $userIds = User::pluck('id')->take(4)->all();
            $pasienIds = Pasien::pluck('id')->all();
            $dokterIds = Dokter::pluck('id')->all();
            $lokasiPenyimpanan = ['Rak A1', 'Rak A2', 'Rak A3', 'Rak A4', 'Rak B1', 'Rak B2', 'Rak B3', 'Rak B4', 'Rak C1', 'Rak C2', 'Rak C3', 'Rak C4', 'Rak D1', 'Rak D2', 'Rak D3'];

            if (empty($userIds) || empty($pasienIds) || empty($dokterIds) || $namaObatMap->isEmpty()) {
                return;
            }

            $periodeAwal = Carbon::create(2026, 6, 1);
            $medicineNames = array_keys($monthlyUsage);
            $days = count(reset($monthlyUsage));

            for ($day = 1; $day <= $days; $day++) {
                $tanggal = $periodeAwal->copy()->addDays($day - 1)->format('Y-m-d');

                $pengeluaran = PengeluaranObat::create([
                    'tanggal_pengeluaran' => $tanggal,
                    'user_id' => $userIds[$day % count($userIds)],
                    'pasien_id' => $pasienIds[$day % count($pasienIds)],
                    'dokter_id' => $dokterIds[$day % count($dokterIds)],
                    'keterangan' => 'Pengeluaran obat bulan Juni ' . $periodeAwal->copy()->addDays($day - 1)->format('d F Y'),
                ]);

                foreach ($monthlyUsage as $namaObatNama => $quantities) {
                    if (! isset($quantities[$day - 1])) {
                        continue;
                    }

                    $namaObat = $namaObatMap->get($namaObatNama);
                    if (! $namaObat) {
                        continue;
                    }

                    $stok = $stokByNamaObatId->get($namaObat->id)?->first();
                    $satuanId = $namaObat->satuan_obat_id ?? SatuanObat::first()?->id;

                    DetailPengeluaranObat::create([
                        'pengeluaran_obat_id' => $pengeluaran->id,
                        'nama_obat_id' => $namaObat->id,
                        'jumlah_keluar' => $quantities[$day - 1],
                        'satuan_id' => $satuanId,
                        'stok_obat_id' => $stok?->id,
                        'lokasi_penyimpanan' => $stok?->lokasi_penyimpanan ?? $lokasiPenyimpanan[array_rand($lokasiPenyimpanan)],
                    ]);
                }
            }
        });
    }
}
