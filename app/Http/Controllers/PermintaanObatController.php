<?php

namespace App\Http\Controllers;

use App\Models\NamaObat;
use App\Models\DetailPenerimaanObat;
use App\Models\DetailPengeluaranObat;
use App\Models\DetailPemusnahanObat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PermintaanObatController extends Controller
{
    public function index(Request $request)
    {
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

        $monthStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $isReportFinal = $now->greaterThanOrEqualTo($monthEnd->copy()->endOfDay());
        $reportNotice = $isReportFinal
            ? null
            : 'Periode belum ditutup. Hasil cetak masih draft.';

        $monthOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $monthLabel = $monthOptions[$selectedMonth];

        $previousMonth = Carbon::create($selectedYear, $selectedMonth, 1)->subMonth();
        $previousMonthLabel = $monthOptions[$previousMonth->month] . ' ' . $previousMonth->year;

        $yearOptions = range($maxYear, $minYear);

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

        // Compute available opening stock as of the start of the selected month.
        // We consider batches received on-or-before month start, still having stok>0
        // and with expiry more than 30 days after the month start.
        $thresholdStartDate = $monthStart->copy()->addDays(30)->toDateString();
        $availableStocksAtStart = DB::table('stok_obat')
            ->select('nama_obat_id', DB::raw('SUM(stok) as available_stok'))
            ->where('stok', '>', 0)
            ->where('tanggal_kadaluwarsa', '>', $thresholdStartDate)
            ->where(function ($q) use ($monthStart) {
                // prefer explicit penerimaan date column if available, otherwise fall back to created_at
                if (Schema::hasColumn('stok_obat', 'tanggal_penerimaan')) {
                    $q->where('tanggal_penerimaan', '<=', $monthStart->toDateString());
                } else {
                    $q->where('created_at', '<=', $monthStart->endOfDay());
                }
            })
            ->groupBy('nama_obat_id')
            ->pluck('available_stok', 'nama_obat_id');

        $startDate = $monthEnd->copy()->subDays($period - 1)->startOfDay();

        $previousMonthStart = $monthStart->copy()->subMonth()->startOfDay();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();

        $periodAverages = DetailPengeluaranObat::select(
            'detail_pengeluaran_obat.nama_obat_id',
            DB::raw('SUM(detail_pengeluaran_obat.jumlah_keluar) as total_keluar'),
            DB::raw('COUNT(DISTINCT pengeluaran_obat.tanggal_pengeluaran) as days_used')
        )
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->whereBetween('pengeluaran_obat.tanggal_pengeluaran', [
                $startDate->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('detail_pengeluaran_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $monthlyIncoming = DetailPenerimaanObat::select(
            'detail_penerimaan_obat.nama_obat_id',
            DB::raw('SUM(detail_penerimaan_obat.jumlah_masuk) as total_masuk')
        )
            ->join('penerimaan_obat', 'penerimaan_obat.id', '=', 'detail_penerimaan_obat.penerimaan_obat_id')
            ->whereBetween('penerimaan_obat.tanggal_penerimaan', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('detail_penerimaan_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $monthlyUsage = DetailPengeluaranObat::select(
            'detail_pengeluaran_obat.nama_obat_id',
            DB::raw('SUM(detail_pengeluaran_obat.jumlah_keluar) as total_keluar')
        )
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->whereBetween('pengeluaran_obat.tanggal_pengeluaran', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('detail_pengeluaran_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $incomingBeforeMonth = DetailPenerimaanObat::select(
            'detail_penerimaan_obat.nama_obat_id',
            DB::raw('SUM(detail_penerimaan_obat.jumlah_masuk) as total_masuk')
        )
            ->join('penerimaan_obat', 'penerimaan_obat.id', '=', 'detail_penerimaan_obat.penerimaan_obat_id')
            ->where('penerimaan_obat.tanggal_penerimaan', '<', $monthStart->toDateString())
            ->groupBy('detail_penerimaan_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $usageBeforeMonth = DetailPengeluaranObat::select(
            'detail_pengeluaran_obat.nama_obat_id',
            DB::raw('SUM(detail_pengeluaran_obat.jumlah_keluar) as total_keluar')
        )
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->where('pengeluaran_obat.tanggal_pengeluaran', '<', $monthStart->toDateString())
            ->groupBy('detail_pengeluaran_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $pemusnahanMonthly = DetailPemusnahanObat::select(
            'detail_pemusnahan_obat.nama_obat_id',
            DB::raw('SUM(detail_pemusnahan_obat.jumlah) as total_dimusnahkan')
        )
            ->join('pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
            ->whereIn('pemusnahan_obat.status', ['approved', 'dimusnahkan'])
            ->whereRaw(
                'DATE(COALESCE(pemusnahan_obat.approved_at, pemusnahan_obat.tanggal_pemusnahan, pemusnahan_obat.created_at)) BETWEEN ? AND ?',
                [$monthStart->toDateString(), $monthEnd->toDateString()]
            )
            ->groupBy('detail_pemusnahan_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $pemusnahanBeforeMonth = DetailPemusnahanObat::select(
            'detail_pemusnahan_obat.nama_obat_id',
            DB::raw('SUM(detail_pemusnahan_obat.jumlah) as total_dimusnahkan')
        )
            ->join('pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
            ->whereIn('pemusnahan_obat.status', ['approved', 'dimusnahkan'])
            ->whereRaw(
                'DATE(COALESCE(pemusnahan_obat.approved_at, pemusnahan_obat.tanggal_pemusnahan, pemusnahan_obat.created_at)) < ?',
                [$monthStart->toDateString()]
            )
            ->groupBy('detail_pemusnahan_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $previousMonthIncoming = DetailPenerimaanObat::select(
            'detail_penerimaan_obat.nama_obat_id',
            DB::raw('SUM(detail_penerimaan_obat.jumlah_masuk) as total_masuk')
        )
            ->join('penerimaan_obat', 'penerimaan_obat.id', '=', 'detail_penerimaan_obat.penerimaan_obat_id')
            ->whereBetween('penerimaan_obat.tanggal_penerimaan', [
                $previousMonthStart->toDateString(),
                $previousMonthEnd->toDateString(),
            ])
            ->groupBy('detail_penerimaan_obat.nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $namaObats = NamaObat::with([
            'minMaxRecords' => function ($builder) use ($selectedYear, $selectedMonth) {
                $builder->where('periode_year', $selectedYear)
                    ->where('periode_month', $selectedMonth);
            },
            'satuanObat',
        ])->orderBy('nama_obat')->get();

        $items = $namaObats->map(function ($obat) use ($periodAverages, $monthlyIncoming, $monthlyUsage, $incomingBeforeMonth, $usageBeforeMonth, $pemusnahanMonthly, $pemusnahanBeforeMonth, $previousMonthIncoming, $leadTime, $bufferDays, $availableStocksAtStart, $monthStart) {
            $incomingBefore = (int) ($incomingBeforeMonth->get($obat->id)->total_masuk ?? 0);
            $usageBefore = (int) ($usageBeforeMonth->get($obat->id)->total_keluar ?? 0);
            $pemusnahanBefore = (int) ($pemusnahanBeforeMonth->get($obat->id)->total_dimusnahkan ?? 0);

            // Determine opening stock (stok_awal).
            // Prefer available batches as of month start (expiry > monthStart + 30 days).
            $openingAvailable = $availableStocksAtStart->has($obat->id)
                ? (int) $availableStocksAtStart->get($obat->id)
                : null;
            if (!is_null($openingAvailable)) {
                $stokAwal = max(0, $openingAvailable);
            } else {
                // Fallback to historical balance calculation if no batch-level data
                $stokAwal = max(0, $incomingBefore - $usageBefore - $pemusnahanBefore);
            }
            // Kolom "Pemberian" menampilkan total stok masuk pada periode yang dipilih.
            // Nilai ini dipakai untuk menghitung "Persediaan" = stok awal + pemberian.
            $pemberian = (int) ($monthlyIncoming->get($obat->id)->total_masuk ?? 0);
            $pemberianBulanLalu = (int) ($previousMonthIncoming->get($obat->id)->total_masuk ?? 0);
            $persediaan = $stokAwal + $pemberian;
            $pemakaian = (int) ($monthlyUsage->get($obat->id)->total_keluar ?? 0);
            $pemusnahan = (int) ($pemusnahanMonthly->get($obat->id)->total_dimusnahkan ?? 0);
            // Use transaction-based closing stock for the selected month snapshot.
            // This avoids leaking current batch balances into past month reports.
            $sisaStok = max(0, $stokAwal + $pemberian - $pemakaian - $pemusnahan);

            $minMax = $obat->minMaxRecords->first();

            $minimumStock = $minMax->minimum_stock ?? 0;
            $maximumStock = $minMax->maximum_stock ?? 0;
            $averageDailyUsage = $minMax->average_daily_usage ?? 0;
            $computedLeadTime = $minMax->lead_time ?? $leadTime;
            $reorderPoint = (int) ($minMax->reorder_point ?? max($minimumStock, (int) ceil(($averageDailyUsage * $computedLeadTime) + $bufferDays)));

            $periodData = $periodAverages->get($obat->id);
            if ($periodData && $periodData->days_used > 0) {
                $periodAverage = round($periodData->total_keluar / max(1, $periodData->days_used), 2);
            } else {
                $periodAverage = round($averageDailyUsage, 2);
            }

                if ($sisaStok <= $minimumStock) {
                $status = 'butuh-restock';
                $statusLabel = 'Butuh Restock';
            } elseif ($sisaStok <= max($minimumStock, intval($minimumStock + max(1, ($maximumStock - $minimumStock) * 0.25)))) {
                $status = 'warning';
                $statusLabel = 'Warning';
            } else {
                $status = 'aman';
                $statusLabel = 'Aman';
            }

            return [
                'id' => $obat->id,
                'nama_obat' => $obat->nama_obat,
                'satuan' => $obat->satuanObat->satuan_obat ?? '-',
                'stok_awal' => $stokAwal,
                'pemberian_bulan_lalu' => $pemberianBulanLalu,
                'pemberian' => $pemberian,
                'persediaan' => $persediaan,
                'pemakaian' => $pemakaian,
                'pemusnahan' => $pemusnahan,
                'sisa_stok' => $sisaStok,
                'permintaan' => $reorderPoint,
                'pemberian_pusat' => null,
                'minimum_stock' => $minimumStock,
                'maximum_stock' => $maximumStock,
                'average_daily_usage' => round($averageDailyUsage, 2),
                'period_average' => $periodAverage,
                'reorder_point' => $reorderPoint,
                'status' => $status,
                'status_label' => $statusLabel,
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

        $printType = $request->get('print');
        $isPermintaanPrint = $printType === 'permintaan';

        if ($printType) {
            $pdf = Pdf::loadView(
                'permintaan_obat.print_pdf',
                compact(
                    'items',
                    'period',
                    'leadTime',
                    'bufferDays',
                    'allowedPeriods',
                    'status',
                    'allowedStatuses',
                    'search',
                    'selectedMonth',
                    'selectedYear',
                    'monthLabel',
                    'previousMonthLabel',
                    'monthOptions',
                    'monthStart',
                    'monthEnd',
                    'isReportFinal',
                    'reportNotice',
                    'isPermintaanPrint'
                )
            );
            $pdf->setPaper('a4', 'landscape');
            $filename = $isPermintaanPrint
                ? 'laporan_permintaan_obat_' . date('Y-m-d_His') . '.pdf'
                : 'laporan_pemakaian_obat_' . date('Y-m-d_His') . '.pdf';

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

        return view(
            'permintaan_obat.permintaan_obat',
            compact(
                'items',
                'period',
                'leadTime',
                'bufferDays',
                'allowedPeriods',
                'status',
                'allowedStatuses',
                'search',
                'perPage',
                'perPageOption',
                'selectedMonth',
                'selectedYear',
                'monthYearValue',
                'previousMonthLabel',
                'monthLabel',
                'monthOptions',
                'yearOptions',
                'monthStart',
                'monthEnd',
                'isReportFinal',
                'reportNotice'
            )
        );
    }
}

