<?php

namespace App\Http\Controllers;

use App\Models\NamaObat;
use App\Models\SatuanObat;
use App\Models\JenisObat;
use App\Models\PengeluaranObat;
use App\Models\DetailPengeluaranObat;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\StokObat;
use App\Services\MinMaxService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengeluaranObatController extends Controller
{
    public function index(Request $request)
    {
        $query = PengeluaranObat::with([
            'User',
            'Pasien',
            'Dokter',
            'detailPengeluaranObat.namaObat',
            'detailPengeluaranObat.satuan',
            'detailPengeluaranObat.stokObat'
        ]);

        // Search by pasien name, pasien no_bpjs, dokter name, or nama obat in details
        if ($request->filled('search')) {
            $search = trim($request->search);

            // If the search matches any pasien by name or no_bpjs, prefer filtering by pasien/dokter only.
            // This avoids returning records where the search matches inside medicine names (e.g. "Citra" inside "Bacitrasin").
            $patientMatch = Pasien::where('nama', 'like', "%{$search}%")
                ->orWhere('no_bpjs', 'like', "%{$search}%")
                ->exists();

            if ($patientMatch) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('Pasien', function($subquery) use ($search) {
                        $subquery->where('nama', 'like', "%{$search}%")
                                 ->orWhere('no_bpjs', 'like', "%{$search}%");
                    })->orWhereHas('Dokter', function($subquery) use ($search) {
                        $subquery->where('nama', 'like', "%{$search}%");
                    });
                });
            } else {
                $query->where(function($q) use ($search) {
                    $q->whereHas('Pasien', function($subquery) use ($search) {
                        $subquery->where('nama', 'like', "%{$search}%")
                                 ->orWhere('no_bpjs', 'like', "%{$search}%");
                    })
                    ->orWhereHas('Dokter', function($subquery) use ($search) {
                        $subquery->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('detailPengeluaranObat.namaObat', function($subquery) use ($search) {
                        $escaped = preg_quote(strtolower($search), '/');
                        $pattern = '(^|[^a-z0-9])' . $escaped . '([^a-z0-9]|$)';
                        $subquery->whereRaw("LOWER(nama_obat) REGEXP ?", [$pattern]);
                    });
                });
            }
        }

        // Filter by date range
        if ($request->filled('tanggal_awal')) {
            $query->where('tanggal_pengeluaran', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_pengeluaran', '<=', $request->tanggal_akhir);
        }

        // Sorting
        $sortColumn = $request->get('sort', 'tanggal_pengeluaran');
        $direction = $request->get('direction', 'desc');

        if ($sortColumn === 'tanggal_pengeluaran') {
            $query->orderBy('pengeluaran_obat.tanggal_pengeluaran', $direction);
        } elseif ($sortColumn === 'pasien_id') {
            $query->join('pasien', 'pengeluaran_obat.pasien_id', '=', 'pasien.id')
                  ->select('pengeluaran_obat.*')
                  ->orderBy('pasien.nama', $direction);
        } elseif ($sortColumn === 'dokter_id') {
            $query->join('dokter', 'pengeluaran_obat.dokter_id', '=', 'dokter.id')
                  ->select('pengeluaran_obat.*')
                  ->orderBy('dokter.nama', $direction);
        } elseif ($sortColumn === 'jumlah_item') {
            $query->withCount('detailPengeluaranObat')
                  ->orderBy('detail_pengeluaran_obat_count', $direction);
        }

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $pengeluaranObats = $query->paginate($perPage);

        // Get data for dropdowns
        $namaObats = NamaObat::all();
        $jenisobats = JenisObat::all();
        $satuanobats = SatuanObat::all();
        // For create modal: only active patients
        $pasienList = Pasien::where('status', 'aktif')->orderBy('nama')->get();
        // For edit modal: include all patients so non-active ones can be shown (but edit field will be disabled)
        $pasienListAll = Pasien::orderBy('nama')->get();
        $dokterList = Dokter::where('status', 'aktif')->orderBy('nama')->get();

        return view('pengeluaran_obat.pengeluaran_obat', compact(
            'pengeluaranObats',
            'namaObats',
            'jenisobats',
            'satuanobats',
            'pasienList',
            'pasienListAll',
            'dokterList',
            'perPage',
            'request'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pengeluaran' => 'required|date',
            'pasien_id' => 'required|exists:pasien,id',
            'dokter_id' => 'required|exists:dokter,id',
            'keterangan' => 'nullable|string',
            'details' => 'required|array',
            'details.*.nama_obat_id' => 'required|exists:nama_obat,id',
            'details.*.stok_obat_id' => 'required|exists:stok_obat,id',
            'details.*.jumlah_keluar' => 'required|integer|min:1',
            'details.*.satuan_id' => 'required|exists:satuan_obat,id',
            'details.*.lokasi_penyimpanan' => 'nullable|string',
        ]);

        // Validasi stok untuk setiap detail
        foreach ($validated['details'] as $detail) {
            $stokObat = StokObat::find($detail['stok_obat_id']);
            if ($stokObat->stok < $detail['jumlah_keluar']) {
                return back()->withInput()->with('error', "Stok tidak mencukupi untuk obat. Stok tersedia: {$stokObat->stok}, diminta: {$detail['jumlah_keluar']}");
            }

            // Validasi apakah SEMUA stok untuk obat ini sudah mendekati/melewati kadaluwarsa (<=30 hari)
            $namaObatId = $detail['nama_obat_id'];
            $allStoksForObat = StokObat::where('nama_obat_id', $namaObatId)
                ->where('stok', '>', 0)
                ->get();

            $allNearExpiry = $allStoksForObat->every(function($stok) {
                $daysUntilExpiry = \Carbon\Carbon::today()->diffInDays($stok->tanggal_kadaluwarsa, false);
                return $daysUntilExpiry <= 30;
            });

            if ($allNearExpiry) {
                $namaObat = NamaObat::find($namaObatId);
                return back()->withInput()->with('error', "Obat '{$namaObat->nama_obat}' tidak dapat dikeluarkan karena semua stok sudah mendekati atau melewati masa berlaku.");
            }
        }

        DB::beginTransaction();
        try {
            // Create pengeluaran_obat record
            $pengeluaranObat = PengeluaranObat::create([
                'tanggal_pengeluaran' => $validated['tanggal_pengeluaran'],
                'user_id' => Auth::id(),
                'pasien_id' => $validated['pasien_id'],
                'dokter_id' => $validated['dokter_id'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Create detail records dan update stok
            foreach ($validated['details'] as $detail) {
                DetailPengeluaranObat::create([
                    'pengeluaran_obat_id' => $pengeluaranObat->id,
                    'nama_obat_id' => $detail['nama_obat_id'],
                    'stok_obat_id' => $detail['stok_obat_id'],
                    'jumlah_keluar' => $detail['jumlah_keluar'],
                    'satuan_id' => $detail['satuan_id'],
                    'lokasi_penyimpanan' => $detail['lokasi_penyimpanan'] ?? null,
                ]);

                // Kurangi stok
                $stokObat = StokObat::find($detail['stok_obat_id']);
                $stokObat->decrement('stok', $detail['jumlah_keluar']);

                // Hitung dan simpan Min-Max
                (new MinMaxService())->calculateAndUpdate($detail['nama_obat_id'], $validated['tanggal_pengeluaran']);
            }

            DB::commit();
            return redirect()->route('pengeluaran-obat.index')->with('success', 'Pengeluaran obat berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan pengeluaran obat: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PengeluaranObat $pengeluaranObat)
    {
        $validated = $request->validate([
            'tanggal_pengeluaran' => 'required|date',
            'pasien_id' => 'required|exists:pasien,id',
            'dokter_id' => 'required|exists:dokter,id',
            'keterangan' => 'nullable|string',
            'details' => 'required|array',
            'details.*.nama_obat_id' => 'required|exists:nama_obat,id',
            'details.*.stok_obat_id' => 'required|exists:stok_obat,id',
            'details.*.jumlah_keluar' => 'required|integer|min:1',
            'details.*.satuan_id' => 'required|exists:satuan_obat,id',
            'details.*.lokasi_penyimpanan' => 'nullable|string',
        ]);

        // Validasi stok untuk setiap detail
        foreach ($validated['details'] as $detail) {
            $stokObat = StokObat::find($detail['stok_obat_id']);
            if ($stokObat->stok < $detail['jumlah_keluar']) {
                return back()->withInput()->with('error', "Stok tidak mencukupi untuk obat. Stok tersedia: {$stokObat->stok}, diminta: {$detail['jumlah_keluar']}");
            }

            // Validasi apakah SEMUA stok untuk obat ini sudah mendekati/melewati kadaluwarsa (<=30 hari)
            $namaObatId = $detail['nama_obat_id'];
            $allStoksForObat = StokObat::where('nama_obat_id', $namaObatId)
                ->where('stok', '>', 0)
                ->get();

            $allNearExpiry = $allStoksForObat->every(function($stok) {
                $daysUntilExpiry = \Carbon\Carbon::today()->diffInDays($stok->tanggal_kadaluwarsa, false);
                return $daysUntilExpiry <= 30;
            });

            if ($allNearExpiry) {
                $namaObat = NamaObat::find($namaObatId);
                return back()->withInput()->with('error', "Obat '{$namaObat->nama_obat}' tidak dapat dikeluarkan karena semua stok sudah mendekati atau melewati masa berlaku.");
            }
        }

        DB::beginTransaction();
        try {
            // Revert old stok (add back the old quantities)
            foreach ($pengeluaranObat->detailPengeluaranObat as $oldDetail) {
                $stokObat = StokObat::find($oldDetail->stok_obat_id);
                $stokObat->increment('stok', $oldDetail->jumlah_keluar);
            }

            // Update pengeluaran_obat record
            $pengeluaranObat->update([
                'tanggal_pengeluaran' => $validated['tanggal_pengeluaran'],
                'pasien_id' => $validated['pasien_id'],
                'dokter_id' => $validated['dokter_id'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Delete existing details
            $pengeluaranObat->detailPengeluaranObat()->delete();

            // Create new detail records dan update stok
            foreach ($validated['details'] as $detail) {
                DetailPengeluaranObat::create([
                    'pengeluaran_obat_id' => $pengeluaranObat->id,
                    'nama_obat_id' => $detail['nama_obat_id'],
                    'stok_obat_id' => $detail['stok_obat_id'],
                    'jumlah_keluar' => $detail['jumlah_keluar'],
                    'satuan_id' => $detail['satuan_id'],
                    'lokasi_penyimpanan' => $detail['lokasi_penyimpanan'] ?? null,
                ]);

                // Kurangi stok dengan jumlah baru
                $stokObat = StokObat::find($detail['stok_obat_id']);
                $stokObat->decrement('stok', $detail['jumlah_keluar']);

                // Hitung dan simpan Min-Max
                (new MinMaxService())->calculateAndUpdate($detail['nama_obat_id'], $validated['tanggal_pengeluaran']);
            }

            DB::commit();
            return redirect()->route('pengeluaran-obat.index')->with('success', 'Pengeluaran obat berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengubah pengeluaran obat: ' . $e->getMessage());
        }
    }

    public function destroy(PengeluaranObat $pengeluaranObat)
    {
        try {
            // Revert stok untuk semua detail items
            foreach ($pengeluaranObat->detailPengeluaranObat as $detail) {
                $stokObat = StokObat::find($detail->stok_obat_id);
                if ($stokObat) {
                    $stokObat->increment('stok', $detail->jumlah_keluar);
                }

                // Hitung dan simpan Min-Max
                (new MinMaxService())->calculateAndUpdate($detail->nama_obat_id, $pengeluaranObat->tanggal_pengeluaran);
            }

            $pengeluaranObat->detailPengeluaranObat()->delete();
            $pengeluaranObat->delete();

            return redirect()->route('pengeluaran-obat.index')->with('success', 'Pengeluaran obat berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pengeluaran obat: ' . $e->getMessage());
        }
    }

    public function getStokByObat($namaObatId)
    {
        $cutoff = now()->addDays(30)->toDateString();

        // Get all available stok (stok > 0) including near expiry
        $allRelevantStoks = StokObat::where('nama_obat_id', $namaObatId)
            ->where('stok', '>', 0)
            ->orderByRaw("CASE WHEN tanggal_kadaluwarsa > ? THEN 0 ELSE 1 END, tanggal_kadaluwarsa ASC", [$cutoff])
            ->get();

        // For edit mode: also include stoks that have been depleted but are referenced in existing records
        $usedStoks = DetailPengeluaranObat::where('stok_obat_id', '!=', null)
            ->join('stok_obat', 'detail_pengeluaran_obat.stok_obat_id', '=', 'stok_obat.id')
            ->where('stok_obat.nama_obat_id', $namaObatId)
            ->pluck('stok_obat.id')
            ->toArray();

        if (!empty($usedStoks)) {
            $depletedStoks = StokObat::where('nama_obat_id', $namaObatId)
                ->whereIn('id', $usedStoks)
                ->where('stok', '<=', 0)
                ->get();
            $allRelevantStoks = $allRelevantStoks->concat($depletedStoks);
        }

        // Remove duplicates and sort
        $allRelevantStoks = $allRelevantStoks->unique('id')
            ->sortBy(function($stok) use ($cutoff) {
                $isSafe = $stok->tanggal_kadaluwarsa->toDateString() > $cutoff ? 0 : 1;
                return [$isSafe, $stok->tanggal_kadaluwarsa];
            });

        return response()->json($allRelevantStoks->map(function($stok) {
            $daysUntil = now()->diffInDays($stok->tanggal_kadaluwarsa, false);
            return [
                'id' => $stok->id,
                'tanggal_kadaluwarsa' => $stok->tanggal_kadaluwarsa->format('d M Y'),
                'tanggal_kadaluwarsa_iso' => $stok->tanggal_kadaluwarsa->toDateString(),
                'stok' => $stok->stok,
                'days_until_expiry' => $daysUntil,
                'display' => $stok->tanggal_kadaluwarsa->format('d M Y') . ' | Stok: ' . $stok->stok
            ];
        }));
    }

    public function getSatuanByObat($namaObatId)
    {
        $namaObat = NamaObat::findOrFail($namaObatId);
        $satuan = SatuanObat::findOrFail($namaObat->satuan_obat_id);

        return response()->json([
            'satuan_obat_id' => $namaObat->satuan_obat_id,
            'satuan_obat' => $satuan->satuan_obat
        ]);
    }

    // AJAX search for nama obat (server-side)
    public function searchNamaObat(Request $request)
    {
        $q = $request->get('q', '');
        $results = NamaObat::when($q, function($query) use ($q) {
                $query->where('nama_obat', 'like', "%{$q}%");
            })
            ->orderBy('nama_obat')
            ->limit(50)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_obat' => $item->nama_obat,
                    'lokasi_penyimpanan' => $item->lokasi_penyimpanan ?? null,
                    'satuan_obat_id' => $item->satuan_obat_id ?? null,
                ];
            });

        return response()->json($results);
    }

    // Get detail info for a single NamaObat (AJAX)
    public function getNamaObatDetail($namaObatId)
    {
        $item = NamaObat::findOrFail($namaObatId);
        $satuanName = null;
        if ($item->satuan_obat_id) {
            $s = SatuanObat::find($item->satuan_obat_id);
            $satuanName = $s ? $s->satuan_obat : null;
        }

        return response()->json([
            'id' => $item->id,
            'nama_obat' => $item->nama_obat,
            'lokasi_penyimpanan' => $item->lokasi_penyimpanan ?? null,
            'satuan_obat_id' => $item->satuan_obat_id ?? null,
            'satuan_name' => $satuanName,
        ]);
    }

    // Generate PDF for pengeluaran obat
    public function printPDF($id)
    {
        $pengeluaran = PengeluaranObat::with([
            'user',
            'pasien',
            'dokter',
            'detailPengeluaranObat.namaObat',
            'detailPengeluaranObat.satuan'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pengeluaran_obat.print_pdf', compact('pengeluaran'));

        // Set paper size to A5 (resep size) and margins
        $pdf->setPaper('a5', 'portrait');

        // Generate filename
        $filename = 'pengeluaran_' . $pengeluaran->id . '_' . date('Y-m-d') . '.pdf';

        // Return PDF download
        return $pdf->download($filename);
    }
}
