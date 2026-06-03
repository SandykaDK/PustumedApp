<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\NamaObat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPengeluaranObat;

class MinMaxService
{
    /**
     * Calculate and update MinMax values for a given obat
     * Called after penerimaan or pengeluaran transaksi
     */
    public function calculateAndUpdate($namaObatId, $recordDate = null)
    {
        $namaObat = NamaObat::findOrFail($namaObatId);
        $asOfDate = $recordDate ? Carbon::parse($recordDate) : Carbon::now();
        $periodeYear = (int) $asOfDate->year;
        $periodeMonth = (int) $asOfDate->month;
        $monthStart = $asOfDate->copy()->startOfMonth()->startOfDay();
        $monthEnd = $asOfDate->copy()->endOfMonth()->endOfDay();

        // Debugging: Cek berapa jumlah detail yang ada
        $totalDetail = DB::table('detail_pengeluaran_obat')
            ->where('nama_obat_id', $namaObatId)
            ->count();
        Log::info("MinMax Calculate - nama_obat_id: $namaObatId, total detail: $totalDetail");

        // Ambil pemakaian bulanan untuk obat ini, lalu hitung per hari
        $usageSummary = $this->getMonthlyUsageSummary($namaObatId, $monthStart, $monthEnd);

        $totalMonthlyUsage = (int) ($usageSummary->total_qty ?? 0);
        $usageDays = (int) ($usageSummary->usage_days ?? 0);
        $maxPerDay = (int) ($usageSummary->max_daily_usage ?? 0);
        $rataRataPengeluaran = round($usageDays > 0 ? ($totalMonthlyUsage / $usageDays) : 0, 2);

        Log::info("Monthly usage for obat $namaObatId: total=$totalMonthlyUsage, usage_days=$usageDays, avg=$rataRataPengeluaran, max_daily=$maxPerDay");

        // Jika belum ada data pada bulan berjalan, pakai fallback terakhir sebelum tanggal kalkulasi
        if ($totalMonthlyUsage === 0) {
            $rataRataPengeluaran = (float) ($this->getLastWithdrawal($namaObatId, $asOfDate) ?? 0);
            $maxPerDay = (int) $rataRataPengeluaran;
            Log::info("Fallback monthly usage for obat $namaObatId: avg=$rataRataPengeluaran, max_daily=$maxPerDay");
        }

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
        $minMax = $namaObat->minMaxRecords()->updateOrCreate(
            [
                'nama_obat_id' => $namaObatId,
                'periode_year' => $periodeYear,
                'periode_month' => $periodeMonth,
            ],
            [
                'periode_year' => $periodeYear,
                'periode_month' => $periodeMonth,
                'average_daily_usage' => $rataRataPengeluaran,
                'maximum_daily_usage' => $maxPerDay,
                'minimum_stock' => $minimumStock,
                'maximum_stock' => $maximumStock,
                'safety_stock' => $safetyStock,
                'reorder_point' => $reorderPoint,
                'lead_time' => $leadTime,
            ]
        );

        return $minMax;
    }

    /**
     * Get monthly total and max daily usage for the selected month.
     */
    public function getMonthlyUsageSummary($namaObatId, Carbon $monthStart, Carbon $monthEnd)
    {
        $dailyUsage = DetailPengeluaranObat::query()
            ->select(
                'detail_pengeluaran_obat.nama_obat_id',
                DB::raw('DATE(pengeluaran_obat.tanggal_pengeluaran) as usage_date'),
                DB::raw('SUM(detail_pengeluaran_obat.jumlah_keluar) as daily_total')
            )
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->where('detail_pengeluaran_obat.nama_obat_id', $namaObatId)
            ->whereBetween('pengeluaran_obat.tanggal_pengeluaran', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('detail_pengeluaran_obat.nama_obat_id', DB::raw('DATE(pengeluaran_obat.tanggal_pengeluaran)'))
            ->orderBy('usage_date')
            ->get();

        return (object) [
            'total_qty' => (int) $dailyUsage->sum('daily_total'),
            'usage_days' => (int) $dailyUsage->count(),
            'max_daily_usage' => (int) $dailyUsage->max('daily_total'),
        ];
    }

    /**
     * Get the last withdrawal amount (fallback jika tidak ada data 30 hari)
     */
    public function getLastWithdrawal($namaObatId, Carbon $asOfDate)
    {
        $lastWithdrawal = DB::table('detail_pengeluaran_obat')
            ->where('nama_obat_id', $namaObatId)
            ->where('created_at', '<=', $asOfDate)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastWithdrawal ? (int) $lastWithdrawal->jumlah_keluar : null;
    }
}
