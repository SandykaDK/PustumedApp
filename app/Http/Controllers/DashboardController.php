<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\JenisObat;
use App\Models\NamaObat;
use App\Models\Pasien;
use App\Models\PenerimaanObat;
use App\Models\PengeluaranObat;
use App\Models\PemusnahanObat;
use App\Models\SatuanObat;
use App\Models\StokObat;
use App\Models\DetailPengeluaranObat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();

        if (!$user) {
            abort(403);
        }

        $data = $this->buildDashboardData($user);

        // Handle AJAX requests for petugas_obat dashboard chart filters
        if (request()->header('X-Requested-With') === 'XMLHttpRequest' && $data['dashboardType'] === 'petugas_obat') {
            $today = Carbon::today();
            $response = [];

            // Check if receipts year filter is being requested
            if (request()->has('year_receipts')) {
                $chartYear = (int) request('year_receipts', $today->year);
                $chartMonths = [];
                $chartReceiptsData = [];

                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = Carbon::createFromDate($chartYear, $month, 1);
                    $chartMonths[] = $monthDate->format('M Y');
                    $chartReceiptsData[] = PenerimaanObat::whereBetween('tanggal_penerimaan', [
                        $monthDate->copy()->startOfMonth()->toDateString(),
                        $monthDate->copy()->endOfMonth()->toDateString(),
                    ])->count();
                }
                $response['chartMonths'] = $chartMonths;
                $response['chartReceiptsData'] = $chartReceiptsData;
            }

            // Check if issues year filter is being requested
            if (request()->has('year_issues')) {
                $chartYear = (int) request('year_issues', $today->year);
                $chartMonths = [];
                $chartIssuesData = [];

                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = Carbon::createFromDate($chartYear, $month, 1);
                    $chartMonths[] = $monthDate->format('M Y');
                    $chartIssuesData[] = PengeluaranObat::whereBetween('tanggal_pengeluaran', [
                        $monthDate->copy()->startOfMonth()->toDateString(),
                        $monthDate->copy()->endOfMonth()->toDateString(),
                    ])->count();
                }
                $response['chartMonths'] = $chartMonths;
                $response['chartIssuesData'] = $chartIssuesData;
            }

            return response()->json($response);
        }

        // Handle AJAX requests for kepala_pustu dashboard chart filters
        if (request()->header('X-Requested-With') === 'XMLHttpRequest' && $data['dashboardType'] === 'kepala_pustu') {
            $today = Carbon::today();
            $response = [];

            // Check if receipts year filter is being requested
            if (request()->has('chart_year_receipts')) {
                $chartYear = (int) request('chart_year_receipts', $today->year);
                $chartMonths = [];
                $chartReceiptsData = [];

                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = Carbon::createFromDate($chartYear, $month, 1)->locale('id');
                    $chartMonths[] = $monthDate->translatedFormat('M Y');
                    $chartReceiptsData[] = PenerimaanObat::whereBetween('tanggal_penerimaan', [
                        $monthDate->copy()->startOfMonth()->toDateString(),
                        $monthDate->copy()->endOfMonth()->toDateString(),
                    ])->count();
                }
                $response['chartReceiptsMonths'] = $chartMonths;
                $response['chartReceiptsData'] = $chartReceiptsData;
            }

            // Check if issues year filter is being requested
            if (request()->has('chart_year_issues')) {
                $chartYear = (int) request('chart_year_issues', $today->year);
                $chartMonths = [];
                $chartIssuesData = [];

                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = Carbon::createFromDate($chartYear, $month, 1)->locale('id');
                    $chartMonths[] = $monthDate->translatedFormat('M Y');
                    $chartIssuesData[] = PengeluaranObat::whereBetween('tanggal_pengeluaran', [
                        $monthDate->copy()->startOfMonth()->toDateString(),
                        $monthDate->copy()->endOfMonth()->toDateString(),
                    ])->count();
                }
                $response['chartIssuesMonths'] = $chartMonths;
                $response['chartIssuesData'] = $chartIssuesData;
            }

            // Check if topused year filter is being requested
            if (request()->has('chart_year_topused')) {
                $chartYear = (int) request('chart_year_topused', $today->year);
                $chartStart = Carbon::createFromDate($chartYear, 1, 1)->startOfYear()->toDateString();
                $chartEnd = Carbon::createFromDate($chartYear, 12, 31)->endOfYear()->toDateString();

                $topUsedQuery = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
                    ->whereHas('pengeluaranObat', function ($query) use ($chartStart, $chartEnd) {
                        $query->whereBetween('tanggal_pengeluaran', [$chartStart, $chartEnd]);
                    })
                    ->groupBy('nama_obat_id')
                    ->orderByDesc('total')
                    ->with('namaObat')
                    ->limit(10)
                    ->get();

                $topUsedLabels = $topUsedQuery->map(fn($r) => $r->namaObat?->nama_obat ?? '—')->toArray();
                $topUsedData = $topUsedQuery->map(fn($r) => (int)$r->total)->toArray();

                $response['topUsedLabels'] = $topUsedLabels;
                $response['topUsedData'] = $topUsedData;
            }

            // Check if fastslow year filter is being requested
            if (request()->has('chart_year_fastslow')) {
                $chartYear = (int) request('chart_year_fastslow', $today->year);
                $chartStart = Carbon::createFromDate($chartYear, 1, 1)->startOfYear()->toDateString();
                $chartEnd = Carbon::createFromDate($chartYear, 12, 31)->endOfYear()->toDateString();

                $fastMoving = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
                    ->whereHas('pengeluaranObat', function ($query) use ($chartStart, $chartEnd) {
                        $query->whereBetween('tanggal_pengeluaran', [$chartStart, $chartEnd]);
                    })
                    ->with('namaObat')
                    ->groupBy('nama_obat_id')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();

                $slowMoving = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
                    ->whereHas('pengeluaranObat', function ($query) use ($chartStart, $chartEnd) {
                        $query->whereBetween('tanggal_pengeluaran', [$chartStart, $chartEnd]);
                    })
                    ->with('namaObat')
                    ->groupBy('nama_obat_id')
                    ->orderBy('total')
                    ->limit(5)
                    ->get();

                $response['fastMoving'] = $fastMoving;
                $response['slowMoving'] = $slowMoving;
            }

            return response()->json($response);
        }

        return view('dashboard', $data);
    }

    private function buildDashboardData($user): array
    {
        return match ($user->role) {
            'petugas_obat' => array_merge(['user' => $user], $this->buildPetugasObatDashboard()),
            'petugas_administrasi' => array_merge(['user' => $user], $this->buildPetugasAdministrasiDashboard()),
            'kepala_pustu' => array_merge(['user' => $user], $this->buildKepalaPustuDashboard()),
            default => [
                'user' => $user,
                'dashboardType' => 'default',
                'dashboardTitle' => 'Dashboard',
                'dashboardDescription' => 'Anda telah berhasil login ke PustumedApp. Gunakan menu di sisi kiri untuk mengakses fitur-fitur aplikasi kami.',
                'dashboardAccentClass' => 'default-hero',
                'dashboardBadge' => 'Role: ' . str($user->role)->headline(),
                'dashboardStats' => [
                    [
                        'label' => 'Status Akun',
                        'value' => 'Aktif',
                        'icon' => '✅',
                        'tone' => 'green',
                    ],
                ],
                'dashboardHighlights' => [],
                'quickActions' => [],
            ],
        };
    }

    private function buildPetugasObatDashboard(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $next30Days = $today->copy()->addDays(30);

        $totalJenisObat = NamaObat::count();
        $totalStokAktif = (int) StokObat::sum('stok');

        $stockSummary = StokObat::query()
            ->select(
                'nama_obat_id',
                DB::raw('SUM(stok) as total_stok'),
                DB::raw("MIN(CASE WHEN stok > 0 THEN tanggal_kadaluwarsa END) as nearest_expiry"),
                DB::raw("SUM(CASE WHEN stok > 0 AND tanggal_kadaluwarsa < '{$today->toDateString()}' THEN 1 ELSE 0 END) as expired_batches"),
                DB::raw("SUM(CASE WHEN stok > 0 AND tanggal_kadaluwarsa BETWEEN '{$today->toDateString()}' AND '{$next30Days->toDateString()}' THEN 1 ELSE 0 END) as expiring_batches")
            )
            ->groupBy('nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $stockByObat = NamaObat::with('minMax')->orderBy('nama_obat')->get();

        $priorityItems = $stockByObat->map(function ($obat) use ($stockSummary, $next30Days, $today) {
            $summary = $stockSummary->get($obat->id);
            $totalStok = (int) ($summary->total_stok ?? 0);
            $minimumStock = (int) ($obat->minMax?->minimum_stock ?? 0);
            $nearestExpiryRaw = $summary?->nearest_expiry ?? null;
            $nearestExpiry = $nearestExpiryRaw ? Carbon::parse($nearestExpiryRaw) : null;
            $expiredBatches = (int) ($summary->expired_batches ?? 0);
            $expiringBatches = (int) ($summary->expiring_batches ?? 0);

            $status = 'Aman';
            $tone = 'success';
            $sortOrder = 4;

            if ($totalStok <= 0) {
                $status = 'Stok Habis';
                $tone = 'danger';
                $sortOrder = 0;
            } elseif ($minimumStock > 0 && $totalStok <= $minimumStock) {
                $status = 'Perlu Pengadaan';
                $tone = 'warning';
                $sortOrder = 1;
            } elseif ($expiredBatches > 0 || ($nearestExpiry && $nearestExpiry->lessThan($today))) {
                $status = 'Sudah Kadaluarsa';
                $tone = 'danger';
                $sortOrder = 2;
            } elseif ($expiringBatches > 0 || ($nearestExpiry && $nearestExpiry->lessThanOrEqualTo($next30Days))) {
                $status = 'Mendekati Kadaluarsa';
                $tone = 'warning';
                $sortOrder = 3;
            }

            return [
                'nama_obat' => $obat->nama_obat,
                'stok' => $totalStok,
                'minimum_stock' => $minimumStock > 0 ? $minimumStock : null,
                'status' => $status,
                'status_key' => match ($status) {
                    'Stok Habis' => 'stok_habis',
                    'Perlu Pengadaan' => 'perlu_pengadaan',
                    'Sudah Kadaluarsa' => 'kadaluarsa',
                    'Mendekati Kadaluarsa' => 'mendekati_kadaluarsa',
                    default => 'belum_minmax',
                },
                'status_label' => $status,
                'tone' => $tone,
                'sort_order' => $sortOrder,
                'nearest_expiry' => $nearestExpiry?->toDateString(),
            ];
        })->filter(function ($item) {
            return $item['sort_order'] < 4;
        })->sort(function (array $left, array $right) {
            return [$left['sort_order'], $left['stok'], $left['nama_obat']] <=> [$right['sort_order'], $right['stok'], $right['nama_obat']];
        })->values();

        $totalStokObat = (int) StokObat::sum('stok');
        $obatStokMinimum = $priorityItems->whereIn('status', ['Stok Habis', 'Perlu Pengadaan'])->count();
        $obatKadaluarsa = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '<', $today->toDateString())
            ->distinct('nama_obat_id')
            ->count('nama_obat_id');
        $obatMendekatiKadaluarsa = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '>=', $today->toDateString())
            ->whereDate('tanggal_kadaluwarsa', '<=', $next30Days->toDateString())
            ->distinct('nama_obat_id')
            ->count('nama_obat_id');
        $transaksiHariIni = PenerimaanObat::whereDate('tanggal_penerimaan', $today->toDateString())->count()
            + PengeluaranObat::whereDate('tanggal_pengeluaran', $today->toDateString())->count();

        // Pemusnahan counts: compute pending/approved/dimusnahkan and candidate stok (belum diajukan)
        $pemusnahanPendingCount = PemusnahanObat::where('status', 'pending')->count();
        $pemusnahanApprovedCount = PemusnahanObat::where('status', 'approved')->count();
        $pemusnahanDimusnahkanCount = PemusnahanObat::where('status', 'dimusnahkan')->count();

        // Determine stok batches near expiry that are not already referenced by a pending pemusnahan request
        $pendingStokIds = \App\Models\DetailPemusnahanObat::whereHas('pemusnahan', function ($q) {
                $q->where('status', 'pending');
            })->pluck('stok_obat_id')->filter()->unique()->toArray();

        $stokCandidatesQuery = StokObat::whereBetween('tanggal_kadaluwarsa', [$today->toDateString(), $next30Days->toDateString()])
            ->where('stok', '>', 0)
            ->when(count($pendingStokIds) > 0, function ($q) use ($pendingStokIds) {
                $q->whereNotIn('id', $pendingStokIds);
            });

        $pemusnahanBelumDiajukanCount = (int) $stokCandidatesQuery->count();

        $chartMonths = [];
        $chartReceiptsData = [];
        $chartIssuesData = [];

        // Get year from request for receipts, default to current year
        $chartYearReceipts = (int) request('year_receipts', $today->year);

        // Generate chart for receipts - all 12 months of the selected year
        for ($month = 1; $month <= 12; $month++) {
            $monthDate = Carbon::createFromDate($chartYearReceipts, $month, 1);
            $chartMonths[] = $monthDate->format('M Y');
            $chartReceiptsData[] = PenerimaanObat::whereBetween('tanggal_penerimaan', [
                $monthDate->copy()->startOfMonth()->toDateString(),
                $monthDate->copy()->endOfMonth()->toDateString(),
            ])->count();
        }

        // Generate chart for issues - all 12 months of the selected year
        $chartYearIssues = (int) request('year_issues', $today->year);
        $chartIssuesMonths = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthDate = Carbon::createFromDate($chartYearIssues, $month, 1);
            $chartIssuesMonths[] = $monthDate->format('M Y');
            $chartIssuesData[] = PengeluaranObat::whereBetween('tanggal_pengeluaran', [
                $monthDate->copy()->startOfMonth()->toDateString(),
                $monthDate->copy()->endOfMonth()->toDateString(),
            ])->count();
        }

        $lowStockCandidates = $priorityItems->whereIn('status', ['Stok Habis', 'Perlu Pengadaan'])->take(3)->values();
        $expiringSoonCandidates = $priorityItems->where('status', 'Mendekati Kadaluarsa')->take(3)->values();

        $pemusnahanApprovalList = PemusnahanObat::with(['user', 'approver'])
            ->withCount('details')
            ->whereIn('status', ['approved', 'dimusnahkan'])
            ->orderByRaw('COALESCE(approved_at, tanggal_pemusnahan, updated_at, created_at) DESC')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $label = $item->status === 'approved' ? 'Approval pemusnahan disetujui' : 'Pemusnahan selesai';

                return [
                    'type' => 'info',
                    'title' => $label,
                    'name' => $item->user?->name ?? 'Pengaju tidak diketahui',
                    'description' => $item->details_count . ' item diproses' . ($item->approver?->name ? ' oleh ' . $item->approver->name : ''),
                    'sort_at' => $item->approved_at ?? $item->tanggal_pemusnahan ?? $item->updated_at ?? $item->created_at,
                ];
            });

        $notifications = collect()
            ->merge($lowStockCandidates->map(function ($item) {
                return [
                    'type' => $item['status'] === 'Stok Habis' ? 'danger' : 'warning',
                    'title' => $item['status'] === 'Stok Habis' ? 'Obat stok habis' : 'Obat stok menipis',
                    'name' => $item['nama_obat'],
                    'description' => 'Stok ' . number_format($item['stok']) . ($item['minimum_stock'] ? ' / min ' . number_format($item['minimum_stock']) : '') . ' - siapkan pengadaan.',
                    'sort_at' => now(),
                ];
            }))
            ->merge($expiringSoonCandidates->map(function ($item) {
                return [
                    'type' => 'danger',
                    'title' => 'Obat akan kadaluarsa',
                    'name' => $item['nama_obat'],
                    'description' => $item['nearest_expiry'] ? 'Tanggal kadaluarsa ' . Carbon::parse($item['nearest_expiry'])->translatedFormat('d M Y') : 'Perlu dicek segera.',
                    'sort_at' => $item['nearest_expiry'] ? Carbon::parse($item['nearest_expiry']) : now(),
                ];
            }))
            ->merge(collect([
                [
                    'type' => 'info',
                    'title' => 'Permintaan obat baru',
                    'name' => 'Prioritas pengadaan',
                    'description' => $obatStokMinimum . ' obat perlu diajukan pengadaan berdasarkan stok minimum.',
                    'sort_at' => now()->subMinutes(1),
                ],
            ]))
            ->sortByDesc(function ($item) {
                return optional(data_get($item, 'sort_at'))->timestamp ?? 0;
            })
            ->values();

        $recentReceipts = PenerimaanObat::with(['User', 'detailPenerimaanObat.namaObat'])
            ->orderByDesc('tanggal_penerimaan')
            ->limit(5)
            ->get()
            ->map(function ($receipt) {
                $items = $receipt->detailPenerimaanObat
                    ->map(fn ($detail) => $detail->namaObat?->nama_obat)
                    ->filter()
                    ->take(3)
                    ->implode(', ');

                $total = (int) $receipt->detailPenerimaanObat->sum('jumlah_masuk');

                return [
                    'title' => $receipt->no_batch ?? 'Penerimaan',
                    'date' => $receipt->tanggal_penerimaan,
                    'user' => $receipt->User?->name ?? '-',
                    'items' => $items ?: '-',
                    'total' => $total,
                ];
            });

        $recentIssues = PengeluaranObat::with(['User', 'detailPengeluaranObat.namaObat'])
            ->orderByDesc('tanggal_pengeluaran')
            ->limit(5)
            ->get()
            ->map(function ($issue) {
                $items = $issue->detailPengeluaranObat
                    ->map(fn ($detail) => $detail->namaObat?->nama_obat)
                    ->filter()
                    ->take(3)
                    ->implode(', ');

                $total = (int) $issue->detailPengeluaranObat->sum('jumlah_keluar');

                return [
                    'title' => $issue->tanggal_pengeluaran ? Carbon::parse($issue->tanggal_pengeluaran)->translatedFormat('d M Y') : 'Pengeluaran',
                    'date' => $issue->tanggal_pengeluaran,
                    'user' => $issue->User?->name ?? '-',
                    'items' => $items ?: '-',
                    'total' => $total,
                ];
            });

        return [
            'dashboardType' => 'petugas_obat',
            'dashboardTitle' => 'Dashboard Petugas Obat',
            'dashboardDescription' => 'Fokus pada pengelolaan stok, pemantauan kedaluwarsa, dan prioritas pengadaan obat harian.',
            'dashboardAccentClass' => 'petugas-obat-hero',
            'dashboardBadge' => 'Role: Petugas Obat',
            'dashboardStats' => [
                ['label' => 'Jumlah Daftar Obat', 'value' => number_format($totalJenisObat), 'icon' => '💊', 'tone' => 'blue'],
                ['label' => 'Obat stok minimum', 'value' => number_format($obatStokMinimum), 'icon' => '⚠️', 'tone' => 'orange'],
                ['label' => 'Obat kadaluarsa', 'value' => number_format($obatKadaluarsa), 'icon' => '⛔', 'tone' => 'red'],
                ['label' => 'Mendekati kadaluarsa', 'value' => number_format($obatMendekatiKadaluarsa), 'icon' => '⏳', 'tone' => 'purple'],
            ],
            'dashboardHighlights' => [
                ['label' => 'Obat perlu pengadaan', 'value' => number_format($obatStokMinimum), 'description' => 'Item yang stoknya sudah berada di batas minimum.'],
                ['label' => 'Obat mendekati kadaluarsa', 'value' => number_format($obatMendekatiKadaluarsa), 'description' => 'Butuh pemantauan agar tidak menjadi stok mati.'],
                ['label' => 'Transaksi hari ini', 'value' => number_format($transaksiHariIni), 'description' => 'Gabungan penerimaan dan pengeluaran hari ini.'],
            ],
            'priorityItems' => $priorityItems,
            'notifications' => $notifications,
            'pemusnahanCounts' => [
                'belum_diajukan' => $pemusnahanBelumDiajukanCount,
                'sudah_diajukan' => $pemusnahanPendingCount,
                'sudah_disetujui' => $pemusnahanApprovedCount,
                'sudah_dimusnahkan' => $pemusnahanDimusnahkanCount,
            ],
            'chartMonths' => $chartMonths,
            'chartReceiptsData' => $chartReceiptsData,
            'chartIssuesData' => $chartIssuesData,
            'recentReceipts' => $recentReceipts,
            'recentIssues' => $recentIssues,
            'quickActions' => [
                ['label' => 'Penerimaan Obat', 'url' => route('penerimaan-obat.index'), 'icon' => '⬇️'],
                ['label' => 'Pengeluaran Obat', 'url' => route('pengeluaran-obat.index'), 'icon' => '💊'],
                ['label' => 'Pemusnahan Obat', 'url' => route('pemusnahan-obat.index'), 'icon' => '🗑️'],
                ['label' => 'Laporan Kadaluwarsa', 'url' => route('laporan-obat-kadaluwarsa.index'), 'icon' => '⚠️'],
            ],
        ];
    }

    private function buildPetugasAdministrasiDashboard(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $next90Days = $today->copy()->addDays(90);

        $totalTransaksiPengeluaranHariIni = PengeluaranObat::whereDate('tanggal_pengeluaran', $today)->count();
        $totalTransaksiPengeluaranBulanIni = PengeluaranObat::whereBetween('tanggal_pengeluaran', [
            $monthStart->toDateString(),
            $monthEnd->toDateString(),
        ])->count();

        $jumlahObatKeluarHariIni = (int) DetailPengeluaranObat::join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->whereDate('pengeluaran_obat.tanggal_pengeluaran', $today->toDateString())
            ->sum('detail_pengeluaran_obat.jumlah_keluar');

        $stockSummary = StokObat::select(
                'nama_obat_id',
                DB::raw('SUM(stok) as total_stok'),
                DB::raw('MIN(tanggal_kadaluwarsa) as nearest_expiry')
            )
            ->where('stok', '>', 0)
            ->groupBy('nama_obat_id')
            ->get()
            ->keyBy('nama_obat_id');

        $stockByObat = NamaObat::with('minMax')->orderBy('nama_obat')->get();

        $stockItems = $stockByObat->map(function ($obat) use ($stockSummary, $today, $next90Days) {
            $summary = $stockSummary->get($obat->id);
            $stok = (int) ($summary->total_stok ?? 0);
            $minimumStock = (int) ($obat->minMax?->minimum_stock ?? 0);
            $nearestExpiry = $summary?->nearest_expiry ? Carbon::parse($summary->nearest_expiry) : null;

            $status = 'Aman';
            $tone = 'success';
            $indicator = 'Hijau';

            if ($stok <= 0) {
                $status = 'Habis';
                $tone = 'danger';
                $indicator = 'Merah';
            } elseif ($minimumStock > 0 && $stok <= $minimumStock) {
                $status = 'Hampir Habis';
                $tone = 'warning';
                $indicator = 'Kuning';
            }

            return [
                'nama_obat' => $obat->nama_obat,
                'stok' => $stok,
                'minimum_stock' => $minimumStock,
                'status' => $status,
                'tone' => $tone,
                'indicator' => $indicator,
                'nearest_expiry' => $nearestExpiry?->toDateString(),
            ];
        });

        $lowStockItems = $stockItems->whereIn('status', ['Habis', 'Hampir Habis']);
        $lowStockCount = $lowStockItems->count();

        $expiringSoonItems = StokObat::where('stok', '>', 0)
            ->whereBetween('tanggal_kadaluwarsa', [
                $today->toDateString(),
                $next90Days->toDateString(),
            ])
            ->with('namaObat')
            ->orderBy('tanggal_kadaluwarsa')
            ->get()
            ->unique('nama_obat_id')
            ->values()
            ->map(function ($item) {
                return [
                    'nama_obat' => $item->namaObat?->nama_obat ?? '—',
                    'tanggal_kadaluwarsa' => $item->tanggal_kadaluwarsa,
                ];
            });

        $jumlahObatAkanKadaluarsa = $expiringSoonItems->count();
        $jumlahPermintaanObatBelumDiproses = $lowStockItems->count();

        $chartMonths = [];
        $chartUsageData = [];
        $chartYear = request('chart_year', $today->year);
        for ($month = 1; $month <= 12; $month++) {
            $monthDate = Carbon::createFromDate($chartYear, $month, 1);
            $chartMonths[] = $monthDate->format('M Y');
            $chartUsageData[] = (int) DetailPengeluaranObat::join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
                ->whereBetween('pengeluaran_obat.tanggal_pengeluaran', [
                    $monthDate->copy()->startOfMonth()->toDateString(),
                    $monthDate->copy()->endOfMonth()->toDateString(),
                ])
                ->sum('detail_pengeluaran_obat.jumlah_keluar');
        }

        $topIssuedObat = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total_keluar'))
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->groupBy('nama_obat_id')
            ->orderByDesc('total_keluar')
            ->with('namaObat')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'nama_obat' => $item->namaObat?->nama_obat ?? '—',
                    'jumlah_keluar' => (int) $item->total_keluar,
                ];
            });

        $recentIssues = PengeluaranObat::with(['detailPengeluaranObat.namaObat'])
            ->orderByDesc('tanggal_pengeluaran')
            ->limit(8)
            ->get()
            ->map(function ($issue) {
                $items = $issue->detailPengeluaranObat
                    ->map(fn ($detail) => $detail->namaObat?->nama_obat)
                    ->filter()
                    ->take(3)
                    ->implode(', ');

                return [
                    'tanggal' => $issue->tanggal_pengeluaran ? Carbon::parse($issue->tanggal_pengeluaran)->translatedFormat('d M Y') : '-',
                    'items' => $items ?: '-',
                    'jumlah' => (int) $issue->detailPengeluaranObat->sum('jumlah_keluar'),
                ];
            });

        $jenisObatKeluarHariIni = DetailPengeluaranObat::join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->whereDate('pengeluaran_obat.tanggal_pengeluaran', $today->toDateString())
            ->distinct('detail_pengeluaran_obat.nama_obat_id')
            ->count('detail_pengeluaran_obat.nama_obat_id');

        $totalItemKeluarHariIni = $jumlahObatKeluarHariIni;

        $topObatHariIni = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total_keluar'))
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->whereDate('pengeluaran_obat.tanggal_pengeluaran', $today->toDateString())
            ->groupBy('nama_obat_id')
            ->orderByDesc('total_keluar')
            ->with('namaObat')
            ->first();

        $bottomObatHariIni = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total_keluar'))
            ->join('pengeluaran_obat', 'pengeluaran_obat.id', '=', 'detail_pengeluaran_obat.pengeluaran_obat_id')
            ->whereDate('pengeluaran_obat.tanggal_pengeluaran', $today->toDateString())
            ->groupBy('nama_obat_id')
            ->orderBy('total_keluar')
            ->with('namaObat')
            ->first();

        // Query patient visit summary berdasarkan filter bulan/tahun
        $filterMonth = request('month', $today->month);
        $filterYear = request('year', $today->year);
        $filterMonthStart = Carbon::createFromDate($filterYear, $filterMonth, 1)->startOfMonth();
        $filterMonthEnd = Carbon::createFromDate($filterYear, $filterMonth, 1)->endOfMonth();

        $patientVisitSummary = PengeluaranObat::join('pasien', 'pengeluaran_obat.pasien_id', '=', 'pasien.id')
            ->whereBetween('pengeluaran_obat.tanggal_pengeluaran', [
                $filterMonthStart->toDateString(),
                $filterMonthEnd->toDateString(),
            ])
            ->select(
                'pasien.nama as nama_pasien',
                DB::raw('COUNT(pengeluaran_obat.id) as jumlah_kedatangan')
            )
            ->groupBy('pasien.id', 'pasien.nama')
            ->orderByDesc('jumlah_kedatangan')
            ->get()
            ->map(fn($item) => [
                'nama_pasien' => $item->nama_pasien ?? '-',
                'jumlah_kedatangan' => $item->jumlah_kedatangan,
            ])
            ->toArray();

        return [
            'dashboardType' => 'petugas_administrasi',
            'dashboardTitle' => 'Dashboard Petugas Administrasi',
            'dashboardDescription' => 'Pantau data pengeluaran obat, permintaan, dan kondisi stok untuk mendukung operasional administrasi.',
            'dashboardAccentClass' => 'administrasi-hero',
            'dashboardBadge' => 'Role: Petugas Administrasi',
            'dashboardStats' => [
                ['label' => 'Total Transaksi Pengeluaran Hari Ini', 'value' => number_format($totalTransaksiPengeluaranHariIni), 'icon' => '📅', 'tone' => 'blue'],
                ['label' => 'Total Transaksi Bulan Ini', 'value' => number_format($totalTransaksiPengeluaranBulanIni), 'icon' => '🗓️', 'tone' => 'purple'],
                ['label' => 'Obat Stok Menipis', 'value' => number_format($lowStockCount), 'icon' => '⚠️', 'tone' => 'orange'],
                ['label' => 'Obat Akan Kadaluarsa', 'value' => number_format($jumlahObatAkanKadaluarsa), 'icon' => '⏳', 'tone' => 'red'],
            ],
            'dashboardHighlights' => [
                ['label' => 'Total jenis obat keluar', 'value' => number_format($jenisObatKeluarHariIni), 'description' => 'Jenis obat yang tercatat keluar hari ini.'],
                ['label' => 'Total item keluar', 'value' => number_format($totalItemKeluarHariIni), 'description' => 'Jumlah unit obat yang keluar hari ini.'],
                ['label' => 'Obat paling banyak keluar', 'value' => $topObatHariIni?->namaObat?->nama_obat ?? '-', 'description' => 'Obat dengan jumlah keluar tertinggi hari ini.'],
                ['label' => 'Obat paling sedikit keluar', 'value' => $bottomObatHariIni?->namaObat?->nama_obat ?? '-', 'description' => 'Obat dengan jumlah keluar terendah hari ini.'],
            ],
            'chartMonths' => $chartMonths,
            'chartUsageData' => $chartUsageData,
            'patientVisitSummary' => $patientVisitSummary,
            'topIssuedObat' => $topIssuedObat,
            'recentIssues' => $recentIssues,
            'stockAlerts' => $stockItems->sortBy('stok')->take(8)->values(),
            'expiringSoonItems' => $expiringSoonItems->take(8)->values(),
            'quickActions' => [
                ['label' => 'Data Pasien', 'url' => route('pasien.index'), 'icon' => '🧑‍⚕️'],
                ['label' => 'Data Dokter', 'url' => route('dokter.index'), 'icon' => '👨‍⚕️'],
                ['label' => 'Laporan Permintaan Obat', 'url' => route('permintaan-obat.index'), 'icon' => '📄'],
                ['label' => 'Data Obat', 'url' => route('nama-obat.index'), 'icon' => '💊'],
            ],
        ];
    }

    private function buildKepalaPustuDashboard(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalJenisObat = NamaObat::count();
        $totalStokAktif = StokObat::where('stok', '>', 0)->sum('stok');
        $stokKosong = StokObat::where('stok', '<=', 0)->count();
        $willExpireCount = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '<=', $now->copy()->addMonths(6)->toDateString())
            ->count();
        $expiredCount = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '<', $now->toDateString())
            ->count();

        $transactionsThisMonth = PenerimaanObat::whereBetween('tanggal_penerimaan', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])->count()
            + PengeluaranObat::whereBetween('tanggal_pengeluaran', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])->count();

        $destructionsThisMonth = PemusnahanObat::whereBetween('tanggal_pemusnahan', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])->count();

        $totalPenerimaan = PenerimaanObat::count();
        $totalPengeluaran = PengeluaranObat::count();
        $totalPemusnahan = PemusnahanObat::count();
        $pemusnahanPending = PemusnahanObat::where('status', 'pending')->count();
        $pemusnahanApproved = PemusnahanObat::where('status', 'approved')->count();

        // Chart year filters
        $chartReceiptsYear = (int) request('chart_year_receipts', $now->year);
        $chartIssuesYear = (int) request('chart_year_issues', $now->year);
        $chartTopUsedYear = (int) request('chart_year_topused', $now->year);
        $chartFastSlowYear = (int) request('chart_year_fastslow', $now->year);
        $chartYearOptions = range($now->year, $now->year - 5);

        $chartReceiptsMonths = [];
        $chartReceiptsData = [];
        for ($month = 1; $month <= 12; $month++) {
            $m = Carbon::createFromDate($chartReceiptsYear, $month, 1)->locale('id');
            $chartReceiptsMonths[] = $m->translatedFormat('M Y');
            $s = $m->copy()->startOfMonth()->toDateString();
            $e = $m->copy()->endOfMonth()->toDateString();
            $chartReceiptsData[] = PenerimaanObat::whereBetween('tanggal_penerimaan', [$s, $e])->count();
        }

        $chartIssuesMonths = [];
        $chartIssuesData = [];
        for ($month = 1; $month <= 12; $month++) {
            $m = Carbon::createFromDate($chartIssuesYear, $month, 1)->locale('id');
            $chartIssuesMonths[] = $m->translatedFormat('M Y');
            $s = $m->copy()->startOfMonth()->toDateString();
            $e = $m->copy()->endOfMonth()->toDateString();
            $chartIssuesData[] = PengeluaranObat::whereBetween('tanggal_pengeluaran', [$s, $e])->count();
        }

        $chartTopUsedStart = Carbon::createFromDate($chartTopUsedYear, 1, 1)->startOfYear()->toDateString();
        $chartTopUsedEnd = Carbon::createFromDate($chartTopUsedYear, 12, 31)->endOfYear()->toDateString();

        $chartFastSlowStart = Carbon::createFromDate($chartFastSlowYear, 1, 1)->startOfYear()->toDateString();
        $chartFastSlowEnd = Carbon::createFromDate($chartFastSlowYear, 12, 31)->endOfYear()->toDateString();

        $topUsedQuery = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
            ->whereHas('pengeluaranObat', function ($query) use ($chartTopUsedStart, $chartTopUsedEnd) {
                $query->whereBetween('tanggal_pengeluaran', [$chartTopUsedStart, $chartTopUsedEnd]);
            })
            ->groupBy('nama_obat_id')
            ->orderByDesc('total')
            ->with('namaObat')
            ->limit(10)
            ->get();

        $topUsedLabels = $topUsedQuery->map(fn($r) => $r->namaObat?->nama_obat ?? '—')->toArray();
        $topUsedData = $topUsedQuery->map(fn($r) => (int)$r->total)->toArray();

        $fastMoving = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
            ->whereHas('pengeluaranObat', function ($query) use ($chartFastSlowStart, $chartFastSlowEnd) {
                $query->whereBetween('tanggal_pengeluaran', [$chartFastSlowStart, $chartFastSlowEnd]);
            })
            ->with('namaObat')
            ->groupBy('nama_obat_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $slowMoving = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
            ->whereHas('pengeluaranObat', function ($query) use ($chartFastSlowStart, $chartFastSlowEnd) {
                $query->whereBetween('tanggal_pengeluaran', [$chartFastSlowStart, $chartFastSlowEnd]);
            })
            ->with('namaObat')
            ->groupBy('nama_obat_id')
            ->orderBy('total')
            ->limit(5)
            ->get();

        // Top 10 used obat (by jumlah_keluar)
        $topUsedQuery = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
            ->groupBy('nama_obat_id')
            ->orderByDesc('total')
            ->with('namaObat')
            ->limit(10)
            ->get();

        // Notifications
        $lowStock = DB::table('min_max')->join('nama_obat', 'min_max.nama_obat_id', '=', 'nama_obat.id')
            ->whereRaw('min_max.minimum_stock > (select COALESCE(SUM(stok),0) from stok_obat where nama_obat_id = nama_obat.id)')
            ->select('nama_obat.id', 'nama_obat.nama_obat as name', 'min_max.created_at as sort_at')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'warning',
                    'title' => 'Stok rendah',
                    'name' => $item->name,
                    'description' => 'Buka data obat untuk pengecekan stok dan min-max',
                    'sort_at' => $item->sort_at,
                ];
            });

        $expiringSoon = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '<=', $now->copy()->addMonths(6)->toDateString())
            ->orderBy('tanggal_kadaluwarsa')
            ->limit(10)
            ->with('namaObat')
            ->get(['id', 'nama_obat_id', 'stok', 'tanggal_kadaluwarsa', 'created_at'])
            ->map(function ($item) {
                return [
                    'type' => 'danger',
                    'title' => 'Akan kadaluwarsa',
                    'name' => $item->namaObat?->nama_obat ?? $item->nama_obat_id,
                    'tanggal_kadaluwarsa' => $item->tanggal_kadaluwarsa,
                    'description' => 'Buka detail obat untuk cek batch dan masa berlaku',
                    'sort_at' => $item->created_at,
                ];
            });

        $permintaanPending = collect();
        $pemusnahanPendingList = PemusnahanObat::with('user')
            ->withCount('details')
            ->where('status', 'pending')
            ->limit(10)
            ->get()
            ->map(function ($item) {
            $jumlahItem = (int) ($item->details_count ?? 0);

            return [
                'type' => 'info',
                'title' => 'Pemusnahan pending',
                'name' => $item->user?->name ?? 'Pengaju tidak diketahui',
                'description' => $jumlahItem > 0
                    ? $jumlahItem . ' item obat menunggu persetujuan'
                    : 'Menunggu persetujuan pemusnahan',
                'sort_at' => $item->created_at,
            ];
        });

        $notifications = collect()
            ->merge($lowStock)
            ->merge($expiringSoon)
            ->merge($pemusnahanPendingList)
            ->sortByDesc(function ($item) {
                return optional(data_get($item, 'sort_at'))->timestamp ?? 0;
            })
            ->values();

        // Recent activity
        $recentReceipts = PenerimaanObat::withCount('detailPenerimaanObat')->orderByDesc('tanggal_penerimaan')->limit(5)->get();
        $recentIssues = PengeluaranObat::withCount('detailPengeluaranObat')->orderByDesc('tanggal_pengeluaran')->limit(5)->get();
        $recentDestructions = PemusnahanObat::orderByDesc('tanggal_pemusnahan')->limit(5)->get();

        return [
            'dashboardType' => 'kepala_pustu',
            'dashboardTitle' => 'Dashboard Kepala Pustu',
            'dashboardDescription' => 'Ringkasan kondisi operasional, status stok, dan aktivitas layanan untuk mendukung pengambilan keputusan.',
            'dashboardAccentClass' => 'kepala-pustu-hero',
            'dashboardBadge' => 'Role: Kepala Pustu',
            'dashboardStats' => [
                ['label' => 'Stok Aktif', 'value' => number_format($totalStokAktif), 'icon' => '📦', 'tone' => 'blue'],
                ['label' => 'Penerimaan', 'value' => number_format($totalPenerimaan), 'icon' => '⬇️', 'tone' => 'green'],
                ['label' => 'Pengeluaran', 'value' => number_format($totalPengeluaran), 'icon' => '⬆️', 'tone' => 'purple'],
                ['label' => 'Pemusnahan', 'value' => number_format($totalPemusnahan), 'icon' => '🗑️', 'tone' => 'orange'],
            ],
            'dashboardHighlights' => [
                ['label' => 'Stok kosong', 'value' => number_format($stokKosong), 'description' => 'Item yang perlu perhatian untuk menjaga ketersediaan layanan.'],
                ['label' => 'Stok dekat kadaluwarsa', 'value' => number_format($willExpireCount), 'description' => 'Perlu dipantau agar tidak menumpuk menjadi risiko layanan.'],
                ['label' => 'Pemusnahan disetujui', 'value' => number_format($pemusnahanApproved), 'description' => 'Jumlah pemusnahan yang sudah melalui persetujuan.'],
                ['label' => 'Pemusnahan pending', 'value' => number_format($pemusnahanPending), 'description' => 'Data yang masih menunggu tindak lanjut.'],
            ],
            'quickActions' => [
                ['label' => 'Laporan Kadaluwarsa', 'url' => route('laporan-obat-kadaluwarsa.index'), 'icon' => '⚠️'],
                ['label' => 'Laporan Pemusnahan', 'url' => route('laporan-pemusnahan-obat.index'), 'icon' => '📄'],
                ['label' => 'Pengeluaran Obat', 'url' => route('pengeluaran-obat.index'), 'icon' => '💊'],
                ['label' => 'Pemusnahan Obat', 'url' => route('pemusnahan-obat.index'), 'icon' => '🗑️'],
            ],
            'totalJenisObat' => $totalJenisObat,
            'totalStokAktif' => $totalStokAktif,
            'willExpireCount' => $willExpireCount,
            'expiredCount' => $expiredCount,
            'transactionsThisMonth' => $transactionsThisMonth,
            'destructionsThisMonth' => $destructionsThisMonth,
            'chartReceiptsYear' => $chartReceiptsYear,
            'chartIssuesYear' => $chartIssuesYear,
            'chartTopUsedYear' => $chartTopUsedYear,
            'chartFastSlowYear' => $chartFastSlowYear,
            'chartYearOptions' => $chartYearOptions,
            'chartReceiptsMonths' => $chartReceiptsMonths,
            'chartIssuesMonths' => $chartIssuesMonths,
            'chartReceiptsData' => $chartReceiptsData,
            'chartIssuesData' => $chartIssuesData,
            'topUsedLabels' => $topUsedLabels,
            'topUsedData' => $topUsedData,
            'fastMoving' => $fastMoving,
            'slowMoving' => $slowMoving,
            'notifications' => $notifications,
            'permintaanPending' => $permintaanPending,
            'recentReceipts' => $recentReceipts,
            'recentIssues' => $recentIssues,
            'recentDestructions' => $recentDestructions,
        ];
    }
}
