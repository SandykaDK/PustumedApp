<?php

namespace App\Http\Controllers;

use App\Models\JenisObat;
use Illuminate\Http\Request;

class JenisObatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['kode_jenis', 'jenis_obat', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $jenisobats = JenisObat::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_jenis', 'like', "%$search%")
                ->orWhere('jenis_obat', 'like', "%$search%");
            });
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage);

        return view('jenis_obat.jenis_obat', compact(
            'jenisobats',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create()
    {
        return view('jenis_obat.create_jenis_obat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jenis'        => 'required|string|max:255',
            'jenis_obat'       => 'required|string|max:255',
        ]);

        JenisObat::create([
            'kode_jenis'       => $request->kode_jenis,
            'jenis_obat'      => $request->jenis_obat,
        ]);

        return redirect()
            ->route('jenis-obat.index')
            ->with('success', 'Jenis Obat berhasil ditambahkan');
    }

    public function edit(JenisObat $jenis_obat)
    {
        return view('jenis_obat.edit_jenis_obat', compact('jenis_obat'));
    }

    public function update(Request $request, JenisObat $jenis_obat)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kode_jenis'        => 'required|string|max:255',
            'jenis_obat'       => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jenis-obat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_jenis_id', $jenis_obat->id);
        }

        $jenis_obat->update($request->only([
            'kode_jenis',
            'jenis_obat'
        ]));

        return redirect()
            ->route('jenis-obat.index')
            ->with('success', 'Jenis Obat berhasil diperbarui');
    }

    // 👉 DELETE
    public function destroy(JenisObat $jenis_obat)
    {
        $jenis_obat->delete();

        return redirect()
            ->route('jenis-obat.index')
            ->with('success', 'Jenis Obat berhasil dihapus');
    }
}
