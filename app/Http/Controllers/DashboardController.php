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
use App\Models\User;

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
        $today = now();
        $monthStart = now()->startOfMonth();
        $next30Days = now()->addDays(30);

        $totalStokAktif = StokObat::where('stok', '>', 0)->sum('stok');
        $stokNearExpiry = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '<=', $next30Days->toDateString())
            ->count();
        $stokKosong = StokObat::where('stok', '<=', 0)->count();
        $penerimaanBulanIni = PenerimaanObat::whereBetween('tanggal_penerimaan', [$monthStart->toDateString(), $today->toDateString()])->count();
        $pengeluaranBulanIni = PengeluaranObat::whereBetween('tanggal_pengeluaran', [$monthStart->toDateString(), $today->toDateString()])->count();
        $pemusnahanPending = PemusnahanObat::where('status', 'pending')->count();

        return [
            'dashboardType' => 'petugas_obat',
            'dashboardTitle' => 'Dashboard Petugas Obat',
            'dashboardDescription' => 'Ringkasan stok, penerimaan, pengeluaran, dan pemantauan obat untuk membantu kerja harian Anda.',
            'dashboardAccentClass' => 'petugas-obat-hero',
            'dashboardBadge' => 'Role: Petugas Obat',
            'dashboardStats' => [
                ['label' => 'Total Stok Tersedia', 'value' => number_format($totalStokAktif), 'icon' => '📦', 'tone' => 'blue'],
                ['label' => 'Item Mendekati Kadaluwarsa', 'value' => number_format($stokNearExpiry), 'icon' => '⏳', 'tone' => 'purple'],
                ['label' => 'Penerimaan Bulan Ini', 'value' => number_format($penerimaanBulanIni), 'icon' => '⬇️', 'tone' => 'green'],
                ['label' => 'Pengeluaran Bulan Ini', 'value' => number_format($pengeluaranBulanIni), 'icon' => '⬆️', 'tone' => 'blue'],
            ],
            'dashboardHighlights' => [
                ['label' => 'Stok kosong', 'value' => number_format($stokKosong), 'description' => 'Perlu dicek untuk restock atau evaluasi pengeluaran.'],
                ['label' => 'Pemusnahan pending', 'value' => number_format($pemusnahanPending), 'description' => 'Tunggu persetujuan atau tindak lanjut pemusnahan.'],
            ],
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
        $totalStokAktif = StokObat::where('stok', '>', 0)->sum('stok');
        $stokKosong = StokObat::where('stok', '<=', 0)->count();
        $stokNearExpiry = StokObat::where('stok', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '<=', now()->addDays(30)->toDateString())
            ->count();
        $totalPenerimaan = PenerimaanObat::count();
        $totalPengeluaran = PengeluaranObat::count();
        $totalPemusnahan = PemusnahanObat::count();
        $pemusnahanPending = PemusnahanObat::where('status', 'pending')->count();
        $pemusnahanApproved = PemusnahanObat::where('status', 'approved')->count();

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
                ['label' => 'Stok dekat kadaluwarsa', 'value' => number_format($stokNearExpiry), 'description' => 'Perlu dipantau agar tidak menumpuk menjadi risiko layanan.'],
                ['label' => 'Pemusnahan disetujui', 'value' => number_format($pemusnahanApproved), 'description' => 'Jumlah pemusnahan yang sudah melalui persetujuan.'],
                ['label' => 'Pemusnahan pending', 'value' => number_format($pemusnahanPending), 'description' => 'Data yang masih menunggu tindak lanjut.'],
            ],
            'quickActions' => [
                ['label' => 'Laporan Kadaluwarsa', 'url' => route('laporan-obat-kadaluwarsa.index'), 'icon' => '⚠️'],
                ['label' => 'Laporan Pemusnahan', 'url' => route('laporan-pemusnahan-obat.index'), 'icon' => '📄'],
                ['label' => 'Pengeluaran Obat', 'url' => route('pengeluaran-obat.index'), 'icon' => '💊'],
                ['label' => 'Pemusnahan Obat', 'url' => route('pemusnahan-obat.index'), 'icon' => '🗑️'],
            ],
        ];
    }
}
