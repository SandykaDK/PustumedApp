<?php

namespace App\Http\Controllers;

use App\Models\NamaObat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MinMaxController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $now = Carbon::now();

        $monthYearInput = (string) $request->get('month_year', '');
        $isValidMonthYear = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthYearInput) === 1;

        $maxYear = (int) $now->year + 1;
        $minYear = (int) $now->year - 5;

        if ($isValidMonthYear) {
            [$pickedYear, $pickedMonth] = explode('-', $monthYearInput);
            $selectedYear = (int) $pickedYear;
            $selectedMonth = (int) $pickedMonth;
        } else {
            $selectedMonth = (int) $request->get('month', $now->month);
            if ($selectedMonth < 1 || $selectedMonth > 12) {
                $selectedMonth = (int) $now->month;
            }

            $selectedYear = (int) $request->get('year', $now->year);
            if ($selectedYear < $minYear || $selectedYear > $maxYear) {
                $selectedYear = (int) $now->year;
            }
        }

        if ($selectedYear < $minYear || $selectedYear > $maxYear) {
            $selectedYear = (int) $now->year;
        }

        $monthYearValue = sprintf('%04d-%02d', $selectedYear, $selectedMonth);

        $yearOptions = range($maxYear, $minYear);

        $allowedStatuses = [
            '' => 'Semua Status',
            'belum-dihitung' => 'Belum dihitung',
            'butuh-restock' => 'Perlu restock',
            'warning' => 'Waspada',
            'aman' => 'Aman'
        ];
        $status = $request->get('status', '');
        if (!array_key_exists($status, $allowedStatuses)) {
            $status = '';
        }

        $perPageInput = $request->get('per_page', 10);
        $allowedPerPage = [10, 25, 50];
        $perPageOption = $perPageInput;

        $query = NamaObat::with([
                'satuanObat',
                'minMaxRecords' => function ($builder) use ($selectedYear, $selectedMonth) {
                    $builder->where('periode_year', $selectedYear)
                        ->where('periode_month', $selectedMonth);
                },
            ])
            ->withSum('stokObat as total_stok', 'stok')
            ->whereHas('minMaxRecords', function ($builder) use ($selectedYear, $selectedMonth) {
                $builder->where('periode_year', $selectedYear)
                    ->where('periode_month', $selectedMonth);
            })
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($subQuery) use ($search) {
                    $subQuery->where('kode_obat', 'like', '%' . $search . '%')
                        ->orWhere('nama_obat', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nama_obat');

        if ($perPageInput === 'all') {
            $perPage = max(1, (clone $query)->count());
            $perPageOption = 'all';
        } else {
            $perPage = (int) $perPageInput;

            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 10;
                $perPageOption = 10;
            }
        }

        $items = $query->paginate($perPage);

        $items->setCollection($items->getCollection()->map(function ($obat) {
            $minMax = $obat->minMaxRecords->first();
            $currentStock = max(0, (int) ($obat->total_stok ?? 0));
            $avg = 0;
            $maxDaily = 0;
            $minimumStock = 0;
            $maximumStock = 0;
            $safetyStock = 0;
            $reorderPoint = 0;
            $statusLabel = 'Belum dihitung';
            $statusClass = 'neutral';
            $statusKey = 'belum-dihitung';

            if ($minMax) {
                $avg = (float) ($minMax->average_daily_usage ?? 0);
                $maxDaily = (int) ($minMax->maximum_daily_usage ?? 0);
                $minimumStock = (int) ($minMax->minimum_stock ?? 0);
                $maximumStock = (int) ($minMax->maximum_stock ?? 0);
                $safetyStock = (int) ($minMax->safety_stock ?? 0);
                $reorderPoint = (int) ($minMax->reorder_point ?? 0);

                if ($currentStock <= $reorderPoint) {
                    $statusLabel = 'Perlu restock';
                    $statusClass = 'danger';
                    $statusKey = 'butuh-restock';
                } elseif ($currentStock <= $minimumStock) {
                    $statusLabel = 'Waspada';
                    $statusClass = 'warning';
                    $statusKey = 'warning';
                } else {
                    $statusLabel = 'Aman';
                    $statusClass = 'success';
                    $statusKey = 'aman';
                }
            }

            return [
                'kode_obat' => $obat->kode_obat,
                'nama_obat' => $obat->nama_obat,
                'satuan' => $obat->satuanObat->satuan_obat ?? '-',
                'stok_saat_ini' => $currentStock,
                'average_daily_usage' => (float) $avg,
                'maximum_daily_usage' => $maxDaily,
                'minimum_stock' => $minimumStock,
                'maximum_stock' => $maximumStock,
                'safety_stock' => $safetyStock,
                'reorder_point' => $reorderPoint,
                'status_label' => $statusLabel,
                'status_class' => $statusClass,
                'status' => $statusKey,
            ];
        }));

        // Apply status filter on the paginated collection (status is computed client-side)
        if (!empty($status)) {
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();

            $collection = $items->getCollection()->filter(function ($it) use ($status) {
                return ($it['status'] ?? '') === $status;
            })->values();

            if ($perPageOption === 'all') {
                $perPage = max(1, $collection->count());
            }

            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->slice(($currentPage - 1) * $perPage, $perPage)->values(),
                $collection->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->getPathInfo(),
                    'query' => $request->query(),
                ]
            );
        }

        return view('min_max.min_max', compact(
            'items',
            'search',
            'perPageOption',
            'monthYearValue',
            'yearOptions',
            'allowedStatuses',
            'status',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
