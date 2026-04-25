<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->get('status', 'semua');
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['nama', 'alamat', 'no_telepon', 'status', 'no_bpjs', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $dokters = Dokter::withCount('pengeluaranObat')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        })
        ->when($status !== 'semua', function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage);

        return view('dokter.dokter', compact(
            'dokters',
            'search',
            'status',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create()
    {
        return view('dokter.create_dokter');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'alamat'       => 'required|string|max:255',
            'no_telepon'       => 'required|string|max:255',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        Dokter::create([
            'nama'         => $request->nama,
            'alamat'       => $request->alamat,
            'no_telepon'   => $request->no_telepon,
            'status'       => $request->status,
        ]);

        return redirect()
            ->route('dokter.index')
            ->with('success', 'Dokter berhasil ditambahkan');
    }

    public function edit(Dokter $dokter)
    {
        return view('dokter.edit_dokter', compact('dokter'));
    }

    public function update(Request $request, Dokter $dokter)
    {
        $validator = Validator::make($request->all(), [
            'nama'        => 'required|string|max:255',
            'alamat'      => 'required|string|max:255',
            'no_telepon'  => 'required|string|max:255',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->route('dokter.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_dokter_id', $dokter->id);
        }

        $dokter->update($request->only([
            'nama',
            'alamat',
            'no_telepon',
            'status',
        ]));

        return redirect()
            ->route('dokter.index')
            ->with('success', 'Dokter berhasil diperbarui');
    }

    public function destroy(Dokter $dokter)
    {
        if ($dokter->pengeluaranObat()->exists()) {
            return redirect()
                ->route('dokter.index')
                ->with('error', 'Dokter tidak dapat dihapus karena sudah digunakan pada transaksi pengeluaran obat.');
        }

        $dokter->delete();

        return redirect()
            ->route('dokter.index')
            ->with('success', 'Dokter berhasil dihapus');
    }
}
