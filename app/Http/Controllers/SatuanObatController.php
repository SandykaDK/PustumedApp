<?php

namespace App\Http\Controllers;

use App\Models\SatuanObat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SatuanObatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['kode_satuan', 'satuan_obat', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $satuanobats = SatuanObat::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_satuan', 'like', "%$search%")
                ->orWhere('satuan_obat', 'like', "%$search%");
            });
        })
        ->withCount('namaObat')
        ->orderBy($sort, $direction)
        ->paginate($perPage);

        return view('satuan_obat.satuan_obat', compact(
            'satuanobats',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create()
    {
        return view('satuan_obat.create_satuan_obat');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_satuan'        => 'required|string|max:255|unique:satuan_obat,kode_satuan',
            'satuan_obat'       => 'required|string|max:255',
        ], [
            'kode_satuan.unique' => 'Kode Satuan "' . $request->kode_satuan . '" sudah ada.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('satuan-obat.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            SatuanObat::create([
                'kode_satuan'       => $request->kode_satuan,
                'satuan_obat'      => $request->satuan_obat,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('satuan-obat.index')
                    ->withErrors(['kode_satuan' => 'Kode Satuan "' . $request->kode_satuan . '" sudah ada.'])
                    ->withInput();
            }
            throw $exception;
        }

        return redirect()
            ->route('satuan-obat.index')
            ->with('success', 'Satuan Obat berhasil ditambahkan');
    }

    public function edit(SatuanObat $satuan_obat)
    {
        return view('satuan_obat.edit_satuan_obat', compact('satuan_obat'));
    }

    public function update(Request $request, SatuanObat $satuan_obat)
    {
        $validator = Validator::make($request->all(), [
            'kode_satuan'        => 'required|string|max:255|unique:satuan_obat,kode_satuan,' . $satuan_obat->id,
            'satuan_obat'       => 'required|string|max:255',
        ], [
            'kode_satuan.unique' => 'Kode Satuan "' . $request->kode_satuan . '" sudah ada.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('satuan-obat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_satuan_obat_id', $satuan_obat->id);
        }

        try {
            $satuan_obat->update($request->only([
                'kode_satuan',
                'satuan_obat'
            ]));
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('satuan-obat.index')
                    ->withErrors(['kode_satuan' => 'Kode Satuan "' . $request->kode_satuan . '" sudah ada.'])
                    ->withInput()
                    ->with('edit_satuan_obat_id', $satuan_obat->id);
            }
            throw $exception;
        }

        return redirect()
            ->route('satuan-obat.index')
            ->with('success', 'Satuan Obat berhasil diperbarui');
    }

    public function destroy(SatuanObat $satuan_obat)
    {
        if ($satuan_obat->namaObat()->exists()) {
            return redirect()
                ->route('satuan-obat.index')
                ->with('error', 'Satuan Obat tidak bisa dihapus karena sudah digunakan pada Daftar Obat.');
        }

        $satuan_obat->delete();

        return redirect()
            ->route('satuan-obat.index')
            ->with('success', 'Satuan Obat berhasil dihapus');
    }
}
