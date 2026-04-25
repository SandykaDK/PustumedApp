<?php

namespace App\Http\Controllers;

use App\Models\NamaObat;
use App\Models\DetailPengeluaranObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanObatController extends Controller
{
    public function index(Request $request)
    {
        $period = (int) $request->get('period', 30);
        $allowedPeriods = [7, 30, 90];
        if (!in_array($period, $allowedPeriods)) {
            $period = 30;
        }

        $leadTime = (int) $request->get('lead_time', 5);
        $bufferDays = (int) $request->get('buffer_days', 10);
        $perPageInput = $request->get('per_page', 10);
        $perPageOption = $perPageInput;

        if ($perPageInput === 'all') {
            $perPage = null;
        } else {
            $perPage = (int) $perPageInput;
            // Whitelist allowed per_page values
            $allowedPerPage = [10, 25, 50];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 10;
                $perPageOption = 10;
            }
        }

        $status = $request->get('status', '');
        $allowedStatuses = [
            '' => 'Semua Status',
            'butuh-restock' => 'Butuh Restock',
            'warning' => 'Warning',
            'aman' => 'Aman'
        ];
        if (!array_key_exists($status, $allowedStatuses)) {
            $status = '';
        }

        $search = $request->get('search', '');

        $startDate = Carbon::now()->subDays($period)->startOfDay();

        $periodAverages = DetailPengeluaranObat::select('nama_obat_id',
            DB::raw('SUM(jumlah_keluar) as total_keluar'),
            DB::raw('COUNT(DISTINCT DATE(created_at)) as days_used')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $namaObats = NamaObat::with(['minMax', 'stokObat'])->orderBy('nama_obat')->get();

        $items = $namaObats->map(function ($obat) use ($periodAverages, $leadTime, $bufferDays) {
            $stock = (int) $obat->stokObat->sum('stok');
            $minMax = $obat->minMax;

            $minimumStock = $minMax->minimum_stock ?? 0;
            $maximumStock = $minMax->maximum_stock ?? 0;
            $averageDailyUsage = $minMax->average_daily_usage ?? 0;
            $maximumDailyUsage = $minMax->maximum_daily_usage ?? 0;
            $computedLeadTime = $minMax->lead_time ?? $leadTime;

            $periodData = $periodAverages->get($obat->id);
            if ($periodData && $periodData->days_used > 0) {
                $periodAverage = round($periodData->total_keluar / max(1, $periodData->days_used), 2);
            } else {
                $periodAverage = round($averageDailyUsage, 2);
            }

            $desiredLevel = max($minimumStock, ceil($periodAverage * $computedLeadTime + $bufferDays));
            if ($maximumStock > 0) {
                $desiredLevel = min($desiredLevel, $maximumStock);
            }

            if ($stock <= $minimumStock) {
                $status = 'butuh-restock';
                $statusLabel = 'Butuh Restock';
            } elseif ($stock <= max($minimumStock, intval($minimumStock + max(1, ($maximumStock - $minimumStock) * 0.25)))) {
                $status = 'warning';
                $statusLabel = 'Warning';
            } else {
                $status = 'aman';
                $statusLabel = 'Aman';
            }

            $recommendation = max(0, $desiredLevel - $stock);

            return [
                'id' => $obat->id,
                'nama_obat' => $obat->nama_obat,
                'stok' => $stock,
                'minimum_stock' => $minimumStock,
                'maximum_stock' => $maximumStock,
                'average_daily_usage' => round($averageDailyUsage, 2),
                'period_average' => $periodAverage,
                'status' => $status,
                'status_label' => $statusLabel,
                'recommendation' => $recommendation,
                'lead_time' => $computedLeadTime,
                'buffer_days' => $bufferDays,
            ];
        });

        // Filter berdasarkan status jika dipilih
        if (!empty($status)) {
            $items = $items->filter(function ($item) use ($status) {
                return $item['status'] === $status;
            });
        }

        // Filter berdasarkan search nama obat jika ada
        if (!empty($search)) {
            $items = $items->filter(function ($item) use ($search) {
                return stripos($item['nama_obat'], $search) !== false;
            });
        }

        $items = $items->values();

        if ($request->has('print')) {
            $pdf = \PDF::loadView('permintaan_obat.print_pdf', compact('items', 'period', 'leadTime', 'bufferDays', 'allowedPeriods', 'status', 'allowedStatuses', 'search'));
            $pdf->setPaper('a4', 'portrait');
            $filename = 'permintaan_obat_' . date('Y-m-d_His') . '.pdf';

            return $pdf->download($filename);
        }

        // Apply pagination to collection
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $itemsCollection = new \Illuminate\Support\Collection($items);

        if ($perPageOption === 'all') {
            $perPage = max(1, $itemsCollection->count());
        }

        $items = $itemsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $itemsCollection->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->getPathInfo(),
                'query' => $request->query(),
            ]
        );

        return view('permintaan_obat.permintaan_obat', compact('items', 'period', 'leadTime', 'bufferDays', 'allowedPeriods', 'status', 'allowedStatuses', 'search', 'perPage', 'perPageOption'));
    }
}

