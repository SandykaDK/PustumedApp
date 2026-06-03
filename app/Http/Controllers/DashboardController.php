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

        return view('dashboard', $this->buildDashboardData($user));
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
        for ($i = 11; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $chartMonths[] = $month->format('M Y');
            $chartReceiptsData[] = PenerimaanObat::whereBetween('tanggal_penerimaan', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ])->count();
            $chartIssuesData[] = PengeluaranObat::whereBetween('tanggal_pengeluaran', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
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
            ->merge($pemusnahanApprovalList)
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
        $totalPasien = Pasien::count();
        $totalDokter = Dokter::count();
        $totalUser = User::count();
        $totalJenisObat = JenisObat::count();
        $totalSatuanObat = SatuanObat::count();
        $totalNamaObat = NamaObat::count();
        $totalPenerimaan = PenerimaanObat::count();
        $totalPengeluaran = PengeluaranObat::count();

        return [
            'dashboardType' => 'petugas_administrasi',
            'dashboardTitle' => 'Dashboard Petugas Administrasi',
            'dashboardDescription' => 'Pantau data master, pasien, dokter, dan aktivitas administrasi untuk menjaga alur layanan tetap rapi.',
            'dashboardAccentClass' => 'administrasi-hero',
            'dashboardBadge' => 'Role: Petugas Administrasi',
            'dashboardStats' => [
                ['label' => 'Total Pasien', 'value' => number_format($totalPasien), 'icon' => '🧑‍⚕️', 'tone' => 'blue'],
                ['label' => 'Total Dokter', 'value' => number_format($totalDokter), 'icon' => '👨‍⚕️', 'tone' => 'purple'],
                ['label' => 'Data Obat', 'value' => number_format($totalNamaObat), 'icon' => '💊', 'tone' => 'green'],
                ['label' => 'Transaksi Administrasi', 'value' => number_format($totalPenerimaan + $totalPengeluaran), 'icon' => '🧾', 'tone' => 'orange'],
            ],
            'dashboardHighlights' => [
                ['label' => 'Total User', 'value' => number_format($totalUser), 'description' => 'Jumlah akun aktif yang terdaftar di sistem.'],
                ['label' => 'Jenis Obat', 'value' => number_format($totalJenisObat), 'description' => 'Master jenis obat yang sudah siap dipakai.'],
                ['label' => 'Satuan Obat', 'value' => number_format($totalSatuanObat), 'description' => 'Referensi satuan untuk pendataan obat.'],
            ],
            'quickActions' => [
                ['label' => 'Data Pasien', 'url' => route('pasien.index'), 'icon' => '🧑‍⚕️'],
                ['label' => 'Data Dokter', 'url' => route('dokter.index'), 'icon' => '👨‍⚕️'],
                ['label' => 'Data Obat', 'url' => route('nama-obat.index'), 'icon' => '💊'],
                ['label' => 'Data User', 'url' => route('users.index'), 'icon' => '👥'],
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

        // Charts: last 12 months
        $months = [];
        $receiptData = [];
        $issueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $label = $m->format('M Y');
            $months[] = $label;
            $s = $m->copy()->startOfMonth()->toDateString();
            $e = $m->copy()->endOfMonth()->toDateString();
            $receiptData[] = PenerimaanObat::whereBetween('tanggal_penerimaan', [$s, $e])->count();
            $issueData[] = PengeluaranObat::whereBetween('tanggal_pengeluaran', [$s, $e])->count();
        }

        // Top 10 used obat (by jumlah_keluar)
        $topUsedQuery = DetailPengeluaranObat::select('nama_obat_id', DB::raw('SUM(jumlah_keluar) as total'))
            ->groupBy('nama_obat_id')
            ->orderByDesc('total')
            ->with('namaObat')
            ->limit(10)
            ->get();

        $topUsedLabels = $topUsedQuery->map(fn($r) => $r->namaObat?->nama_obat ?? '—')->toArray();
        $topUsedData = $topUsedQuery->map(fn($r) => (int)$r->total)->toArray();

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
            'chartMonths' => $months,
            'chartReceiptsData' => $receiptData,
            'chartIssuesData' => $issueData,
            'topUsedLabels' => $topUsedLabels,
            'topUsedData' => $topUsedData,
            'notifications' => $notifications,
            'permintaanPending' => $permintaanPending,
            'recentReceipts' => $recentReceipts,
            'recentIssues' => $recentIssues,
            'recentDestructions' => $recentDestructions,
        ];
    }
}
