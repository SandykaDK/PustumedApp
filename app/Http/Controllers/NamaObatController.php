<?php

namespace App\Http\Controllers;

use App\Models\NamaObat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\JenisObat;
use App\Models\SatuanObat;

class NamaObatController extends Controller
{
    private function blockManagementForRestrictedRoles()
    {
        if (auth()->check() && auth()->user()?->role === 'kepala_pustu') {
            return redirect()->route('nama-obat.index')->with('error', 'Anda tidak memiliki akses untuk mengubah daftar obat.');
        }

        return null;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $filterJenis = $request->jenis_obat_id;
        $filterSatuan = $request->satuan_obat_id;
        $sort = $request->sort ?? 'id';
        $direction = $request->direction ?? 'asc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['id',
                    'kode_obat',
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
            ->withSum('availableStokObat as total_stok', 'stok')
            ->withCount([
                'detailPenerimaanObat',
                'detailPengeluaranObat',
                'detailPemusnahanObat',
                'stokObat',
                'minMax',
            ])
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
        $blocked = $this->blockManagementForRestrictedRoles();
        if ($blocked) {
            return $blocked;
        }

        return view('nama_obat.create_nama_obat');
    }

    public function store(Request $request)
    {
        $blocked = $this->blockManagementForRestrictedRoles();
        if ($blocked) {
            return $blocked;
        }

        $messages = [
            'nama_obat.required' => 'Nama Obat harus diisi.',
            'nama_obat.string' => 'Nama Obat harus berupa teks.',
            'nama_obat.max' => 'Nama Obat tidak boleh lebih dari 255 karakter.',
            'nama_obat.unique' => 'Nama Obat "' . $request->nama_obat . '" sudah ada.',
            'jenis_obat_id.required' => 'Jenis Obat harus dipilih.',
            'jenis_obat_id.exists' => 'Jenis Obat tidak valid.',
            'satuan_obat_id.required' => 'Satuan Obat harus dipilih.',
            'satuan_obat_id.exists' => 'Satuan Obat tidak valid.',
            'status.required' => 'Status akun harus dipilih.',
            'status.in' => 'Status akun tidak valid.',
        ];

        $validator = Validator::make($request->all(), [
            'nama_obat'       => 'required|string|max:255|unique:nama_obat,nama_obat',
            'jenis_obat_id'  => 'required|exists:jenis_obat,id',
            'satuan_obat_id'  => 'required|exists:satuan_obat,id',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('nama-obat.index')
                ->withErrors($validator)
                ->withInput();
        }

        // generate kode_obat from jenis
        $kode = NamaObat::generateKodeForJenis((int)$request->jenis_obat_id);

        try {
            NamaObat::create([
                'kode_obat'       => $kode,
                'nama_obat'      => $request->nama_obat,
                'jenis_obat_id' => $request->jenis_obat_id,
                'satuan_obat_id' => $request->satuan_obat_id,
                'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
                'status' => $request->input('status', 'aktif'),
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('nama-obat.index')
                    ->withErrors(['nama_obat' => 'Nama Obat "' . $request->nama_obat . '" sudah ada.'])
                    ->withInput();
            }
            throw $exception;
        }

        return redirect()
            ->route('nama-obat.index')
            ->with('success', 'Nama Obat berhasil ditambahkan');
    }

    public function edit(NamaObat $nama_obat)
    {
        $blocked = $this->blockManagementForRestrictedRoles();
        if ($blocked) {
            return $blocked;
        }

        return view('nama_obat.edit_nama_obat', compact('nama_obat'));
    }

    public function update(Request $request, NamaObat $nama_obat)
    {
        $blocked = $this->blockManagementForRestrictedRoles();
        if ($blocked) {
            return $blocked;
        }

        $messages = [
            'nama_obat.required' => 'Nama Obat harus diisi.',
            'nama_obat.string' => 'Nama Obat harus berupa teks.',
            'nama_obat.max' => 'Nama Obat tidak boleh lebih dari 255 karakter.',
            'nama_obat.unique' => 'Nama Obat "' . $request->nama_obat . '" sudah ada.',
            'jenis_obat_id.required' => 'Jenis Obat harus dipilih.',
            'jenis_obat_id.exists' => 'Jenis Obat tidak valid.',
            'satuan_obat_id.required' => 'Satuan Obat harus dipilih.',
            'satuan_obat_id.exists' => 'Satuan Obat tidak valid.',
            'status.required' => 'Status akun harus dipilih.',
            'status.in' => 'Status akun tidak valid.',
        ];

        $validator = Validator::make($request->all(), [
            'nama_obat'       => 'required|string|max:255|unique:nama_obat,nama_obat,' . $nama_obat->id,
            'jenis_obat_id'  => 'required|exists:jenis_obat,id',
            'satuan_obat_id'  => 'required|exists:satuan_obat,id',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('nama-obat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_nama_obat_id', $nama_obat->id);
        }

        $data = $request->only([
            'nama_obat',
            'jenis_obat_id',
            'satuan_obat_id',
            'lokasi_penyimpanan',
            'status',
        ]);

        if (! $request->has('status')) {
            $data['status'] = 'aktif';
        }

        // if jenis changed, generate new kode, otherwise keep existing
        if ((int)$request->jenis_obat_id !== (int)$nama_obat->jenis_obat_id) {
            $data['kode_obat'] = NamaObat::generateKodeForJenis((int)$request->jenis_obat_id, $nama_obat->id);
        }

        try {
            $nama_obat->update($data);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('nama-obat.index')
                    ->withErrors(['nama_obat' => 'Nama Obat "' . $request->nama_obat . '" sudah ada.'])
                    ->withInput()
                    ->with('edit_nama_obat_id', $nama_obat->id);
            }
            throw $exception;
        }

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
                // determine days until expiry
                $today = now()->startOfDay();
                $expiry = $item->tanggal_kadaluwarsa ? $item->tanggal_kadaluwarsa->startOfDay() : null;
                $daysUntil = $expiry ? $today->diffInDays($expiry, false) : null;

                // status rules: more than 30 days => available, 30 days or less => not available
                if (is_null($daysUntil)) {
                    $statusLabel = ucfirst($item->status ?? 'Tidak diketahui');
                    $statusClass = 'status-unknown';
                } else {
                    if ($daysUntil > 30) {
                        $statusLabel = 'Tersedia';
                        $statusClass = 'status-available';
                    } else {
                        $statusLabel = 'Tidak tersedia';
                        $statusClass = 'status-unavailable';
                    }
                }

                return [
                    'no_batch' => $item->no_batch,
                    'tanggal_kadaluwarsa' => $item->tanggal_kadaluwarsa ? $item->tanggal_kadaluwarsa->format('d M Y') : '-',
                    'stok' => $item->stok,
                    'status' => $statusLabel,
                    'status_class' => $statusClass,
                    'days_until' => $daysUntil,
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
        $blocked = $this->blockManagementForRestrictedRoles();
        if ($blocked) {
            return $blocked;
        }

        if ($nama_obat->isInUse()) {
            return redirect()
                ->route('nama-obat.index')
                ->with('error', 'Nama Obat tidak bisa dihapus karena sudah digunakan pada transaksi/data lain.');
        }

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
            ->get()
            ->map(function ($item) {
                $today = now()->startOfDay();
                $expiry = $item->tanggal_kadaluwarsa ? $item->tanggal_kadaluwarsa->startOfDay() : null;
                $daysUntil = $expiry ? $today->diffInDays($expiry, false) : null;

                if (is_null($daysUntil)) {
                    $statusLabel = ucfirst($item->status ?? 'Tidak diketahui');
                    $statusClass = 'status-unknown';
                } else {
                    if ($daysUntil > 30) {
                        $statusLabel = 'Tersedia';
                        $statusClass = 'status-available';
                    } else {
                        $statusLabel = 'Tidak tersedia';
                        $statusClass = 'status-unavailable';
                    }
                }

                return [
                    'no_batch' => $item->no_batch,
                    'tanggal_kadaluwarsa' => $item->tanggal_kadaluwarsa ? $item->tanggal_kadaluwarsa->format('d M Y') : '-',
                    'stok' => $item->stok,
                    'status' => $statusLabel,
                    'status_class' => $statusClass,
                    'days_until' => $daysUntil,
                ];
            });

        return response()->json([
            'nama_obat' => $nama_obat->nama_obat,
            'kode_obat' => $nama_obat->kode_obat,
            'stokItems' => $stokItems,
        ]);
    }

    /**
     * AJAX: generate next kode for a jenis
     */
    public function generateKode($jenisId)
    {
        $kode = NamaObat::generateKodeForJenis((int)$jenisId);
        return response()->json(['kode' => $kode]);
    }
}

