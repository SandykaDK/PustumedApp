<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['nama', 'alamat', 'no_telepon', 'no_bpjs', 'created_at'];
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
        ->orderBy($sort, $direction)
        ->paginate($perPage);

        return view('pasien.pasien', compact(
            'pasiens',
            'search',
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
        $request->validate([
            'nama'        => 'required|string|max:255',
            'nik'       => 'required|string|max:255',
            'alamat'       => 'required|string|max:255',
            'no_telepon'       => 'required|string|max:255',
            'no_bpjs'       => 'required|string|max:255',
        ]);

        Pasien::create([
            'nama'         => $request->nama,
            'nik'          => $request->nik,
            'alamat'       => $request->alamat,
            'no_telepon'   => $request->no_telepon,
            'no_bpjs'      => $request->no_bpjs,
        ]);

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
            'nama'        => 'required|string|max:255',
            'nik'         => 'required|string|max:255',
            'alamat'      => 'required|string|max:255',
            'no_telepon'  => 'required|string|max:255',
            'no_bpjs'     => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pasien.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_pasien_id', $pasien->id);
        }

        $pasien->update($request->only([
            'nama',
            'nik',
            'alamat',
            'no_telepon',
            'no_bpjs'
        ]));

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
