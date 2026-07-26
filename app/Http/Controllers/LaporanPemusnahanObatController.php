<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokObat;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanPemusnahanObatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $sort_by = $request->get('sort_by', 'tanggal_kadaluwarsa');
        $direction = $request->get('direction', 'desc');
        $perPage = $request->get('per_page', 10);

        // Ambil semua obat yang mendekati kadaluwarsa ATAU sudah kadaluwarsa
        // (tanggal_kadaluwarsa <= hari ini + 30)
        $limit = Carbon::today()->addDays(30);

        $stokQuery = StokObat::with([
            'namaObat',
            'detailPemusnahanObat.pemusnahan.user',
            'detailPemusnahanObat.pemusnahan.approver',
            'detailPemusnahanObat.pemusnahan.details',
        ])
            ->whereDate('tanggal_kadaluwarsa', '<=', $limit->toDateString())
            ->where(function ($q) {
                // Tampilkan stok aktif ATAU stok yang sudah punya riwayat pemusnahan
                $q->where('stok', '>', 0)
                  ->orWhereHas('detailPemusnahanObat');
            });

        if ($search) {
            $stokQuery->whereHas('namaObat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%' . $search . '%');
            });
        }

        $stokItems = $stokQuery->get();

        // Bentuk baris laporan gabungan:
        // - Jika stok sudah ada riwayat pemusnahan: tampilkan sesuai record pemusnahan
        // - Jika belum ada riwayat: tampilkan sebagai "Belum Dimusnahkan" dengan tanggal N/A
        $reportRows = collect();
        foreach ($stokItems as $stok) {
            $namaObat = $stok->namaObat->nama_obat ?? 'N/A';
            $tglKadaluwarsa = optional($stok->tanggal_kadaluwarsa)->format('Y-m-d');

            $pemusnahanRecords = $stok->detailPemusnahanObat
                ->map(function ($detail) {
                    return $detail->pemusnahan;
                })
                ->filter()
                ->unique('id')
                ->values();

            if ($pemusnahanRecords->isEmpty()) {
                $reportRows->push([
                    'nama_obat' => $namaObat,
                    'tanggal_kadaluwarsa' => $tglKadaluwarsa,
                    'jumlah' => $stok->stok,
                    'tanggal_pemusnahan' => null,
                    'tanggal_pemusnahan_sort' => null,
                    'pengaju' => 'N/A',
                    'status' => 'pending',
                    'approver' => 'N/A',
                    'keterangan' => '-',
                    'bukti_foto' => null,
                ]);
                continue;
            }

            foreach ($pemusnahanRecords as $pemusnahan) {
                // Hitung total jumlah dari semua detail pemusnahan untuk pemusnahan ini
                $totalJumlah = $pemusnahan->details()->sum('jumlah') ?? 0;

                $reportRows->push([
                    'nama_obat' => $namaObat,
                    'tanggal_kadaluwarsa' => $tglKadaluwarsa,
                    'jumlah' => $totalJumlah,
                    'tanggal_pemusnahan' => optional($pemusnahan->tanggal_pemusnahan)->translatedFormat('d F Y'),
                    'tanggal_pemusnahan_sort' => optional($pemusnahan->tanggal_pemusnahan)->format('Y-m-d H:i:s'),
                    'pengaju' => $pemusnahan->user->name ?? 'N/A',
                    'status' => $pemusnahan->status ?? 'pending',
                    'approver' => $pemusnahan->approver->name ?? 'N/A',
                    'keterangan' => $pemusnahan->keterangan ?? '-',
                    'bukti_foto' => $pemusnahan->bukti_foto,
                ]);
            }
        }

        // Filter status pada data gabungan
        if ($status !== 'all') {
            $reportRows = $reportRows->where('status', $status)->values();
        }

        // Sorting pada data gabungan
        $allowedSorts = ['nama_obat', 'tanggal_kadaluwarsa', 'stok', 'tanggal_pemusnahan', 'status'];
        $sort_by = in_array($sort_by, $allowedSorts) ? $sort_by : 'tanggal_kadaluwarsa';
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $reportRows = $reportRows->sortBy(function ($row) use ($sort_by) {
            if ($sort_by === 'tanggal_pemusnahan') {
                // Null tanggal pemusnahan diset paling kecil agar natural saat sorting desc
                return $row['tanggal_pemusnahan_sort'] ?? '0000-00-00 00:00:00';
            }

            if ($sort_by === 'tanggal_kadaluwarsa') {
                return $row['tanggal_kadaluwarsa'] ?? '0000-00-00';
            }

            return $row[$sort_by] ?? '';
        }, SORT_REGULAR, $direction === 'desc')->values();

        // Pagination collection
        if ($perPage === 'all') {
            $perPageValue = max($reportRows->count(), 1);
        } else {
            $perPageValue = max((int) $perPage, 1);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $reportRows->forPage($currentPage, $perPageValue)->values();
        $pemusnahanObat = new LengthAwarePaginator(
            $currentItems,
            $reportRows->count(),
            $perPageValue,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $allowedStatuses = [
            'all' => 'Semua',
            'pending' => 'Belum Dimusnahkan',
            'approved' => 'Sudah Dimusnahkan',
        ];

        return view('laporan_pemusnahan_obat.laporan_pemusnahan_obat', compact(
            'pemusnahanObat',
            'search',
            'status',
            'sort_by',
            'direction',
            'perPage',
            'allowedStatuses'
        ));
    }
}
