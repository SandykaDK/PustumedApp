<?php

namespace App\Http\Controllers;

use App\Models\StokObat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanObatKadaluwarsaController extends Controller
{
    public function index(Request $request)
    {
        $perPageInput = $request->get('per_page', 10);
        $perPageOption = $perPageInput;

        if ($perPageInput === 'all') {
            $perPage = null;
        } else {
            $perPage = (int) $perPageInput;
            $allowedPerPage = [10, 25, 50];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 10;
                $perPageOption = 10;
            }
        }

        $status = $request->get('status', '');
        $allowedStatuses = [
            '' => 'Semua',
            'warning' => 'Warning',
            'expired' => 'Expired',
        ];

        if (!array_key_exists($status, $allowedStatuses)) {
            $status = '';
        }

        $statusPemusnahan = $request->get('status_pemusnahan', '');
        $allowedPemusnahanStatuses = [
            '' => 'Semua',
            'belum' => 'Belum',
            'sudah' => 'Sudah',
        ];

        if (!array_key_exists($statusPemusnahan, $allowedPemusnahanStatuses)) {
            $statusPemusnahan = '';
        }

        $sort_by = $request->get('sort_by', 'nama_obat');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['nama_obat', 'tanggal_kadaluwarsa', 'sisa_hari', 'stok'];

        if (!in_array($sort_by, $allowedSorts)) {
            $sort_by = 'nama_obat';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $search = $request->get('search', '');

        $today = Carbon::today();
        $limit = Carbon::today()->addDays(30);

        $stokItems = StokObat::with(['namaObat', 'detailPemusnahanObat'])
            ->whereDate('tanggal_kadaluwarsa', '<=', $limit)
            ->where('stok', '>', 0)
            ->orderBy('tanggal_kadaluwarsa', 'asc')
            ->get();

        $items = $stokItems->map(function ($stok) use ($today) {
            $pemusnahanExists = $stok->detailPemusnahanObat->isNotEmpty();
            $diffDays = $today->diffInDays($stok->tanggal_kadaluwarsa, false);
            $statusExp = $diffDays < 0 ? 'Expired' : 'Warning';
            $sisaHari = max(0, $diffDays);

            return [
                'id' => $stok->id,
                'nama_obat' => optional($stok->namaObat)->nama_obat,
                'tanggal_kadaluwarsa' => $stok->tanggal_kadaluwarsa,
                'stok' => $stok->stok,
                'sisa_hari' => $sisaHari,
                'status_exp' => $statusExp,
                'status_pemusnahan' => $pemusnahanExists ? 'Sudah' : 'Belum',
            ];
        });

        if (!empty($search)) {
            $items = $items->filter(function ($item) use ($search) {
                return stripos($item['nama_obat'], $search) !== false;
            });
        }

        if ($status === 'expired') {
            $items = $items->filter(function ($item) {
                return $item['status_exp'] === 'Expired';
            });
        } elseif ($status === 'warning') {
            $items = $items->filter(function ($item) {
                return $item['status_exp'] === 'Warning';
            });
        }

        if ($statusPemusnahan === 'sudah') {
            $items = $items->filter(function ($item) {
                return $item['status_pemusnahan'] === 'Sudah';
            });
        } elseif ($statusPemusnahan === 'belum') {
            $items = $items->filter(function ($item) {
                return $item['status_pemusnahan'] === 'Belum';
            });
        }

        $items = $items->sortBy(function ($item) use ($sort_by) {
            if ($sort_by === 'nama_obat') {
                return strtolower($item['nama_obat'] ?? '');
            }

            return $item[$sort_by] ?? null;
        }, SORT_NATURAL|SORT_FLAG_CASE, $direction === 'desc');

        $items = $items->values();

        if ($request->has('print')) {
            $pdf = \PDF::loadView('laporan_obat_kadaluwarsa.print_pdf', compact('items'));
            $pdf->setPaper('a4', 'portrait');
            $filename = 'laporan_obat_kadaluwarsa_' . date('Y-m-d_His') . '.pdf';

            return $pdf->download($filename);
        }

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

        return view('laporan_obat_kadaluwarsa.laporan_obat_kadaluwarsa', compact('items', 'allowedStatuses', 'status', 'allowedPemusnahanStatuses', 'statusPemusnahan', 'sort_by', 'direction', 'search', 'perPage', 'perPageOption'));
    }
}

