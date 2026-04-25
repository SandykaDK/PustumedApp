<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\NamaObat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DetailPengeluaranObat;

class MinMaxService
{
    /**
     * Calculate and update MinMax values for a given obat
     * Called after penerimaan or pengeluaran transaksi
     */
    public function calculateAndUpdate($namaObatId)
    {
        $namaObat = NamaObat::findOrFail($namaObatId);

        // Debugging: Cek berapa jumlah detail yang ada
        $totalDetail = DetailPengeluaranObat::where('nama_obat_id', $namaObatId)->count();
        Log::info("MinMax Calculate - nama_obat_id: $namaObatId, total detail: $totalDetail");

        // Ambil rata-rata pengeluaran 30 hari terakhir
        $rataRataPengeluaran = $this->getRataRataPengeluaran($namaObatId);
        Log::info("Rata-rata pengeluaran 30 hari: $rataRataPengeluaran");

        // Jika masih 0, gunakan nilai default (bisa dari config atau permintaan terakhir)
        if ($rataRataPengeluaran == 0) {
            $rataRataPengeluaran = $this->getLastWithdrawal($namaObatId) ?? 10;
            Log::info("Rata-rata default: $rataRataPengeluaran");
        }

        // Pemakaian maksimum per transaksi (30 hari terakhir)
        $maxPerDay = $this->getPemakaianMaksimumPerHari($namaObatId);
        Log::info("Max per transaksi 30 hari: $maxPerDay");

        // Lead time (default 5 hari atau dari konfigurasi)
        $leadTime = 5;

        // Safety Stock: (Max Daily Usage - Avg Daily Usage) * Lead Time
        $safetyStockCalc = ($maxPerDay - $rataRataPengeluaran) * $leadTime;
        $safetyStock = (int) ceil(max(0, $safetyStockCalc));

        // Minimum Stock: (Avg Daily Usage * Lead Time) + Safety Stock
        $minimumStock = (int) ceil(($rataRataPengeluaran * $leadTime) + $safetyStock);

        // Maximum Stock: 2 * (Avg Daily Usage * Lead Time) + Safety Stock
        $maximumStock = (int) ceil(2 * ($rataRataPengeluaran * $leadTime) + $safetyStock);

        // Reorder Point (ROP): Maximum Stock - Minimum Stock
        $reorderPoint = (int) ceil($maximumStock - $minimumStock);

        // Save atau update ke tabel min_max
        $namaObat->minMax()->updateOrCreate(
            ['nama_obat_id' => $namaObatId],
            [
                'average_daily_usage' => $rataRataPengeluaran,
                'maximum_daily_usage' => $maxPerDay,
                'minimum_stock' => $minimumStock,
                'maximum_stock' => $maximumStock,
                'safety_stock' => $safetyStock,
                'reorder_point' => $reorderPoint,
                'lead_time' => $leadTime,
            ]
        );

        return $namaObat->minMax()->first();
    }

    /**
     * Get average withdrawal - hitung dari data 30 hari terakhir
     */
    public function getRataRataPengeluaran($namaObatId)
    {
        // Ambil data detail pengeluaran 30 hari terakhir
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $recentDetails = DetailPengeluaranObat::where('nama_obat_id', $namaObatId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();

        Log::info("Detail count for obat $namaObatId in last 30 days: " . $recentDetails->count());

        if ($recentDetails->isEmpty()) {
            Log::info("Tidak ada detail dalam 30 hari terakhir, return 0");
            return 0;
        }

        $totalQty = $recentDetails->sum('jumlah_keluar');
        $totalDays = $recentDetails->count(); // jumlah transaksi dalam 30 hari

        $avg = $totalQty / max($totalDays, 1);
        Log::info("Total qty in 30 days: $totalQty, Total transactions: $totalDays, Avg per transaction: $avg");

        return $avg;
    }

    /**
     * Get maximum usage per transaction - dari data 30 hari terakhir
     */
    public function getPemakaianMaksimumPerHari($namaObatId)
    {
        // Ambil maximum quantity per transaksi dalam 30 hari terakhir
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $maxDetail = DetailPengeluaranObat::where('nama_obat_id', $namaObatId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->orderBy('jumlah_keluar', 'desc')
            ->first();

        $max = $maxDetail ? $maxDetail->jumlah_keluar : 0;
        Log::info("Max per transaksi for obat $namaObatId in last 30 days: $max");

        return $max;
    }

    /**
     * Get the last withdrawal amount (fallback jika tidak ada data 30 hari)
     */
    public function getLastWithdrawal($namaObatId)
    {
        $lastWithdrawal = DetailPengeluaranObat::where('nama_obat_id', $namaObatId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastWithdrawal ? $lastWithdrawal->jumlah_keluar : null;
    }
}
