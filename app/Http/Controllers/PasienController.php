<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['nama', 'alamat', 'no_telepon', 'jenis_kelamin', 'golongan_darah', 'no_bpjs', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $pasiens = Pasien::with([
            'pengeluaranObat.user',
            'pengeluaranObat.dokter',
            'pengeluaranObat.detailPengeluaranObat.namaObat',
            'pengeluaranObat.detailPengeluaranObat.satuan',
        ])
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%")
                ->orWhere('no_bpjs', 'like', "%$search%");
            });
        })
        ->when($status && $status !== 'semua', function ($query) use ($status) {
            $mappedStatus = $status === 'nonaktif' ? 'non-aktif' : $status;

            $query->where('status', $mappedStatus);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage);

        return view('pasien.pasien', compact(
            'pasiens',
            'search',
            'status',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create()
    {
        return view('pasien.create_pasien');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'              => 'required|string|max:255|unique:pasien,nama',
            'nik'               => 'required|string|max:255|unique:pasien,nik',
            'alamat'            => 'required|string|max:255',
            'jenis_kelamin'     => 'required|in:L,P',
            'golongan_darah'    => 'required|in:A,B,AB,O',
            'no_telepon'        => 'required|string|max:255|unique:pasien,no_telepon',
            'no_bpjs'           => 'nullable|string|max:255|unique:pasien,no_bpjs',
            'status'            => 'required|in:aktif,non-aktif',
        ], [
            'nama.unique' => 'Nama Pasien "' . $request->nama . '" sudah ada.',
            'nik.unique' => 'NIK "' . $request->nik . '" sudah ada.',
            'no_telepon.unique' => 'No. Telepon "' . $request->no_telepon . '" sudah ada.',
            'no_bpjs.unique' => 'No. BPJS "' . $request->no_bpjs . '" sudah ada.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pasien.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Pasien::create([
                'nama'          => $request->nama,
                'nik'           => $request->nik,
                'alamat'        => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'golongan_darah'=> $request->golongan_darah,
                'no_telepon'    => $request->no_telepon,
                'no_bpjs'       => $request->no_bpjs,
                'status'        => $request->status ?? 'aktif',
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('pasien.index')
                    ->withErrors(['nama' => 'Data pasien sudah ada.'])
                    ->withInput();
            }
            throw $exception;
        }

        return redirect()
            ->route('pasien.index')
            ->with('success', 'Pasien berhasil ditambahkan');
    }

    public function edit(Pasien $pasien)
    {
        return view('pasien.edit_pasien', compact('pasien'));
    }

    public function update(Request $request, Pasien $pasien)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:255|unique:pasien,nama,' . $pasien->id,
            'nik'           => 'required|string|max:255|unique:pasien,nik,' . $pasien->id,
            'alamat'        => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah'=> 'required|in:A,B,AB,O',
            'no_telepon'    => 'required|string|max:255|unique:pasien,no_telepon,' . $pasien->id,
            'no_bpjs'       => 'nullable|string|max:255|unique:pasien,no_bpjs,' . $pasien->id,
            'status'        => 'required|in:aktif,non-aktif',
        ], [
            'nama.unique' => 'Nama Pasien "' . $request->nama . '" sudah ada.',
            'nik.unique' => 'NIK "' . $request->nik . '" sudah ada.',
            'no_telepon.unique' => 'No. Telepon "' . $request->no_telepon . '" sudah ada.',
            'no_bpjs.unique' => 'No. BPJS "' . $request->no_bpjs . '" sudah ada.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pasien.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_pasien_id', $pasien->id);
        }

        try {
            $pasien->update($request->only([
                'nama',
                'nik',
                'alamat',
                'jenis_kelamin',
                'golongan_darah',
                'no_telepon',
                'no_bpjs',
                'status'
            ]));
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('pasien.index')
                    ->withErrors(['nama' => 'Data pasien sudah ada.'])
                    ->withInput()
                    ->with('edit_pasien_id', $pasien->id);
            }
            throw $exception;
        }

        return redirect()
            ->route('pasien.index')
            ->with('success', 'Pasien berhasil diperbarui');
    }

    public function destroy(Pasien $pasien)
    {
        $pasien->delete();

        return redirect()
            ->route('pasien.index')
            ->with('success', 'Pasien berhasil dihapus');
    }
}
