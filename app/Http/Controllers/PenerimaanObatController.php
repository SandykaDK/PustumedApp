<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanObat;
use App\Models\DetailPenerimaanObat;
use App\Models\NamaObat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\StokObat;
use Carbon\Carbon;

class PenerimaanObatController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->search);
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';

        // whitelist allowed sortable columns
        $allowed = ['no_batch', 'tanggal_penerimaan', 'created_at'];
        if (!in_array($sort, $allowed)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $penerimaanObats = PenerimaanObat::with([
                'user',
                'detailPenerimaanObat.namaObat',
                'detailPenerimaanObat.jenisObat',
                'detailPenerimaanObat.satuan',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_batch', 'like', "%$search%");
                        $q->orWhereHas('detailPenerimaanObat.namaObat', function ($detailQuery) use ($search) {
                            $detailQuery->where('nama_obat', 'like', "%$search%");
                        });
                });
            })
            ->when($tanggal_awal, function ($query) use ($tanggal_awal) {
                $query->where('tanggal_penerimaan', '>=', $tanggal_awal);
            })
            ->when($tanggal_akhir, function ($query) use ($tanggal_akhir) {
                $query->where('tanggal_penerimaan', '<=', $tanggal_akhir);
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage);

        // Load supporting data for dropdowns in modals
        $namaObats = NamaObat::with(['jenisObat', 'satuanObat'])->orderBy('nama_obat')->get();
        $jenisobats = JenisObat::orderBy('jenis_obat')->get();
        $satuanobats = SatuanObat::orderBy('satuan_obat')->get();

        return view('penerimaan_obat.penerimaan_obat', compact(
            'penerimaanObats',
            'search',
            'tanggal_awal',
            'tanggal_akhir',
            'perPage',
            'sort',
            'direction',
            'namaObats',
            'jenisobats',
            'satuanobats'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_penerimaan' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'details' => 'required|array|min:1',
            'details.*.nama_obat_id' => 'required|exists:nama_obat,id',
            'details.*.jenis_obat_id' => 'required|exists:jenis_obat,id',
            'details.*.tanggal_kadaluwarsa' => 'required|date',
            'details.*.jumlah_masuk' => 'required|integer|min:1',
            'details.*.satuan_id' => 'required|exists:satuan_obat,id',
            'details.*.lokasi_penyimpanan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('penerimaan-obat.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Validate duplicate details (same nama_obat_id + tanggal_kadaluwarsa should not repeat)
        $seen = [];
        if ($request->has('details')) {
            foreach ($request->details as $i => $detail) {
                $tgl = isset($detail['tanggal_kadaluwarsa']) ? $detail['tanggal_kadaluwarsa'] : '';
                $key = ($detail['nama_obat_id'] ?? '') . '|' . $tgl;
                if (isset($seen[$key])) {
                    $validator->errors()->add('details.' . $i, 'Item duplikat: nama obat dan tanggal kadaluwarsa tidak boleh sama dengan item lain.');
                    return redirect()->route('penerimaan-obat.index')
                        ->withErrors($validator)
                        ->withInput();
                }
                $seen[$key] = true;
            }
        }

        // Use transaction so stock and details stay consistent
        DB::transaction(function () use ($request) {
            // Create penerimaan_obat record
            $penerimaan = PenerimaanObat::create([
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            // Create detail records and update stok_obat accordingly
            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    // Generate a detail-specific batch identifier using header batch + expiry date
                    $detailBatch = $penerimaan->no_batch . '-' . Carbon::parse($detail['tanggal_kadaluwarsa'])->format('Ymd');

                    $created = DetailPenerimaanObat::create([
                        'penerimaan_obat_id' => $penerimaan->id,
                        'nama_obat_id' => $detail['nama_obat_id'],
                        'jenis_obat_id' => $detail['jenis_obat_id'],
                        'no_batch' => $detailBatch,
                        'tanggal_kadaluwarsa' => $detail['tanggal_kadaluwarsa'],
                        'jumlah_masuk' => $detail['jumlah_masuk'],
                        'satuan_id' => $detail['satuan_id'],
                        'lokasi_penyimpanan' => $detail['lokasi_penyimpanan'],
                    ]);

                    // Update stok_obat: upsert by nama_obat_id + tanggal_kadaluwarsa + detail-specific no_batch
                    $stokRow = StokObat::where('nama_obat_id', $detail['nama_obat_id'])
                        ->where('tanggal_kadaluwarsa', $detail['tanggal_kadaluwarsa'])
                        ->where('no_batch', $detailBatch)
                        ->first();

                    if ($stokRow) {
                        $stokRow->increment('stok', $detail['jumlah_masuk']);
                    } else {
                        StokObat::create([
                            'nama_obat_id' => $detail['nama_obat_id'],
                            'tanggal_kadaluwarsa' => $detail['tanggal_kadaluwarsa'],
                            'stok' => $detail['jumlah_masuk'],
                            'no_batch' => $detailBatch,
                            'keterangan' => null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('penerimaan-obat.index')
            ->with('success', 'Penerimaan obat berhasil ditambahkan');
    }

    public function update(Request $request, PenerimaanObat $penerimaan_obat)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_penerimaan' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'details' => 'required|array|min:1',
            'details.*.nama_obat_id' => 'required|exists:nama_obat,id',
            'details.*.jenis_obat_id' => 'required|exists:jenis_obat,id',
            'details.*.tanggal_kadaluwarsa' => 'required|date',
            'details.*.jumlah_masuk' => 'required|integer|min:1',
            'details.*.satuan_id' => 'required|exists:satuan_obat,id',
            'details.*.lokasi_penyimpanan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('penerimaan-obat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_penerimaan_obat_id', $penerimaan_obat->id);
        }

        // Validate duplicate details (same nama_obat_id + tanggal_kadaluwarsa should not repeat)
        $seen = [];
        if ($request->has('details')) {
            foreach ($request->details as $i => $detail) {
                $tgl = isset($detail['tanggal_kadaluwarsa']) ? $detail['tanggal_kadaluwarsa'] : '';
                $key = ($detail['nama_obat_id'] ?? '') . '|' . $tgl;
                if (isset($seen[$key])) {
                    $validator->errors()->add('details.' . $i, 'Item duplikat: nama obat dan tanggal kadaluwarsa tidak boleh sama dengan item lain.');
                    return redirect()->route('penerimaan-obat.index')
                        ->withErrors($validator)
                        ->withInput()
                        ->with('edit_penerimaan_obat_id', $penerimaan_obat->id);
                }
                $seen[$key] = true;
            }
        }

        // Perform update inside a transaction so stock stays consistent
        DB::transaction(function () use ($request, $penerimaan_obat) {
            // Reverse stock for existing details
            $oldDetails = $penerimaan_obat->detailPenerimaanObat()->get();
            foreach ($oldDetails as $old) {
                // Use the detail's own no_batch when reversing stock
                $stokRow = StokObat::where('nama_obat_id', $old->nama_obat_id)
                    ->where('tanggal_kadaluwarsa', $old->tanggal_kadaluwarsa)
                    ->where('no_batch', $old->no_batch)
                    ->first();

                if ($stokRow) {
                    $stokRow->decrement('stok', $old->jumlah_masuk);
                    if ($stokRow->stok <= 0) {
                        $stokRow->delete();
                    }
                }
            }

            // Delete old details
            DetailPenerimaanObat::where('penerimaan_obat_id', $penerimaan_obat->id)->delete();

            // Update penerimaan header
            $penerimaan_obat->update([
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'keterangan' => $request->keterangan,
            ]);

            // Create new details and apply stock increments
            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    // Generate detail-specific batch
                    $detailBatch = $penerimaan_obat->no_batch . '-' . Carbon::parse($detail['tanggal_kadaluwarsa'])->format('Ymd');

                    DetailPenerimaanObat::create([
                        'penerimaan_obat_id' => $penerimaan_obat->id,
                        'nama_obat_id' => $detail['nama_obat_id'],
                        'jenis_obat_id' => $detail['jenis_obat_id'],
                        'no_batch' => $detailBatch,
                        'tanggal_kadaluwarsa' => $detail['tanggal_kadaluwarsa'],
                        'jumlah_masuk' => $detail['jumlah_masuk'],
                        'satuan_id' => $detail['satuan_id'],
                        'lokasi_penyimpanan' => $detail['lokasi_penyimpanan'],
                    ]);

                    $stokRow = StokObat::where('nama_obat_id', $detail['nama_obat_id'])
                        ->where('tanggal_kadaluwarsa', $detail['tanggal_kadaluwarsa'])
                        ->where('no_batch', $detailBatch)
                        ->first();

                    if ($stokRow) {
                        $stokRow->increment('stok', $detail['jumlah_masuk']);
                    } else {
                        StokObat::create([
                            'nama_obat_id' => $detail['nama_obat_id'],
                            'tanggal_kadaluwarsa' => $detail['tanggal_kadaluwarsa'],
                            'stok' => $detail['jumlah_masuk'],
                            'no_batch' => $detailBatch,
                            'keterangan' => null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('penerimaan-obat.index')
            ->with('success', 'Penerimaan obat berhasil diperbarui');
    }

    public function destroy(PenerimaanObat $penerimaan_obat)
    {
        // Check if this penerimaan has been used in other transactions
        $usageCheck = $penerimaan_obat->checkUsage();

        if ($usageCheck['used']) {
            return redirect()->route('penerimaan-obat.index')
                ->with('error', $usageCheck['message']);
        }

        // Reverse stock for all details before deleting
        DB::transaction(function () use ($penerimaan_obat) {
            $details = $penerimaan_obat->detailPenerimaanObat()->get();
            foreach ($details as $detail) {
                // Use the detail's stored no_batch when reversing stock
                $stokRow = StokObat::where('nama_obat_id', $detail->nama_obat_id)
                    ->where('tanggal_kadaluwarsa', $detail->tanggal_kadaluwarsa)
                    ->where('no_batch', $detail->no_batch)
                    ->first();

                if ($stokRow) {
                    $stokRow->decrement('stok', $detail->jumlah_masuk);
                    if ($stokRow->stok <= 0) {
                        $stokRow->delete();
                    }
                }
            }

            DetailPenerimaanObat::where('penerimaan_obat_id', $penerimaan_obat->id)->delete();
            $penerimaan_obat->delete();
        });

        return redirect()->route('penerimaan-obat.index')
            ->with('success', 'Penerimaan obat berhasil dihapus');
    }
}

