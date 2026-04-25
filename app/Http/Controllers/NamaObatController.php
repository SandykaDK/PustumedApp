<?php

namespace App\Http\Controllers;

use App\Models\NamaObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\JenisObat;
use App\Models\SatuanObat;

class NamaObatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $filterJenis = $request->jenis_obat_id;
        $filterSatuan = $request->satuan_obat_id;
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['kode_obat',
                    'nama_obat',
                    'jenis_obat_id',
                    'satuan_obat_id',
                    'lokasi_penyimpanan',
                    'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $namaobats = NamaObat::with(['jenisObat', 'satuanObat'])
            ->withSum('stokObat as total_stok', 'stok')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_obat', 'like', "%$search%")
                      ->orWhere('nama_obat', 'like', "%$search%");
                });
            })
            ->when($filterJenis, function ($query) use ($filterJenis) {
                $query->where('jenis_obat_id', $filterJenis);
            })
            ->when($filterSatuan, function ($query) use ($filterSatuan) {
                $query->where('satuan_obat_id', $filterSatuan);
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage);


        // lists for selects
        $jenisobats = JenisObat::orderBy('jenis_obat')->get();
        $satuanobats = SatuanObat::orderBy('satuan_obat')->get();

        return view('nama_obat.nama_obat', compact(
            'namaobats',
            'search',
            'filterJenis',
            'filterSatuan',
            'sort',
            'direction',
            'perPage',
            'jenisobats',
            'satuanobats'
        ));
    }

    public function create()
    {
        return view('nama_obat.create_nama_obat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_obat'        => 'required|string|max:255',
            'nama_obat'       => 'required|string|max:255',
            'jenis_obat_id'  => 'required|exists:jenis_obat,id',
            'satuan_obat_id'  => 'required|exists:satuan_obat,id',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
        ]);

        NamaObat::create([
            'kode_obat'       => $request->kode_obat,
            'nama_obat'      => $request->nama_obat,
            'jenis_obat_id' => $request->jenis_obat_id,
            'satuan_obat_id' => $request->satuan_obat_id,
            'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
        ]);

        return redirect()
            ->route('nama-obat.index')
            ->with('success', 'Nama Obat berhasil ditambahkan');
    }

    public function edit(NamaObat $nama_obat)
    {
        return view('nama_obat.edit_nama_obat', compact('nama_obat'));
    }

    public function update(Request $request, NamaObat $nama_obat)
    {
        $validator = Validator::make($request->all(), [
            'kode_obat'        => 'required|string|max:255',
            'nama_obat'       => 'required|string|max:255',
            'jenis_obat_id'  => 'required|exists:jenis_obat,id',
            'satuan_obat_id'  => 'required|exists:satuan_obat,id',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('nama-obat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_nama_obat_id', $nama_obat->id);
        }

        $nama_obat->update($request->only([
            'kode_obat',
            'nama_obat',
            'jenis_obat_id',
            'satuan_obat_id',
            'lokasi_penyimpanan',
        ]));

        return redirect()
            ->route('nama-obat.index')
            ->with('success', 'Nama Obat berhasil diperbarui');
    }

    public function show(NamaObat $nama_obat)
    {
        // load stok > 0 details sorted by expiry date
        $stokItems = $nama_obat->stokObat()
            ->where('stok', '>', 0)
            ->orderBy('tanggal_kadaluwarsa')
            ->get()
            ->map(function ($item) {
                return [
                    'no_batch' => $item->no_batch,
                    'tanggal_kadaluwarsa' => $item->tanggal_kadaluwarsa ? $item->tanggal_kadaluwarsa->format('d M Y') : '-',
                    'stok' => $item->stok,
                    'status' => ucfirst($item->status),
                ];
            });

        return response()->json([
            'nama_obat' => $nama_obat->nama_obat,
            'kode_obat' => $nama_obat->kode_obat,
            'stokItems' => $stokItems,
        ]);
    }

    public function destroy(NamaObat $nama_obat)
    {
        $nama_obat->delete();

        return redirect()
            ->route('nama-obat.index')
            ->with('success', 'Nama Obat berhasil dihapus');
    }

    public function getStokJson(NamaObat $nama_obat)
    {
        $stokItems = $nama_obat->stokObat()
            ->where('stok', '>', 0)
            ->orderBy('tanggal_kadaluwarsa')
            ->get();

        return response()->json(['stokItems' => $stokItems]);
    }
}

