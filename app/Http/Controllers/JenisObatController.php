<?php

namespace App\Http\Controllers;

use App\Models\JenisObat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class JenisObatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'id';
        $direction = $request->direction ?? 'asc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['id', 'kode_jenis', 'jenis_obat', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'id';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $jenisobats = JenisObat::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_jenis', 'like', "%$search%")
                ->orWhere('jenis_obat', 'like', "%$search%");
            });
        })
        ->withCount('namaObat')
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
        $messages = [
            'kode_jenis.required' => 'Kode Jenis harus diisi.',
            'kode_jenis.string' => 'Kode Jenis harus berupa teks.',
            'kode_jenis.max' => 'Kode Jenis tidak boleh lebih dari 255 karakter.',
            'kode_jenis.unique' => 'Kode Jenis "' . $request->kode_jenis . '" sudah ada.',
            'jenis_obat.required' => 'Jenis Obat harus diisi.',
            'jenis_obat.string' => 'Jenis Obat harus berupa teks.',
            'jenis_obat.max' => 'Jenis Obat tidak boleh lebih dari 255 karakter.',
            'jenis_obat.unique' => 'Jenis Obat "' . $request->jenis_obat . '" sudah ada.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kode_jenis'        => 'required|string|max:255|unique:jenis_obat,kode_jenis',
            'jenis_obat'       => 'required|string|max:255|unique:jenis_obat,jenis_obat',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('jenis-obat.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            JenisObat::create([
                'kode_jenis'       => $request->kode_jenis,
                'jenis_obat'      => $request->jenis_obat,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('jenis-obat.index')
                    ->withErrors(['kode_jenis' => 'Kode Jenis "' . $request->kode_jenis . '" atau jenis obat sudah ada.'])
                    ->withInput();
            }
            throw $exception;
        }

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
        $messages = [
            'kode_jenis.required' => 'Kode Jenis harus diisi.',
            'kode_jenis.string' => 'Kode Jenis harus berupa teks.',
            'kode_jenis.max' => 'Kode Jenis tidak boleh lebih dari 255 karakter.',
            'kode_jenis.unique' => 'Kode Jenis "' . $request->kode_jenis . '" sudah ada.',
            'jenis_obat.required' => 'Jenis Obat harus diisi.',
            'jenis_obat.string' => 'Jenis Obat harus berupa teks.',
            'jenis_obat.max' => 'Jenis Obat tidak boleh lebih dari 255 karakter.',
            'jenis_obat.unique' => 'Jenis Obat "' . $request->jenis_obat . '" sudah ada.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kode_jenis'        => 'required|string|max:255|unique:jenis_obat,kode_jenis,' . $jenis_obat->id,
            'jenis_obat'       => 'required|string|max:255|unique:jenis_obat,jenis_obat,' . $jenis_obat->id,
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('jenis-obat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_jenis_id', $jenis_obat->id);
        }

        try {
            $jenis_obat->update($request->only([
                'kode_jenis',
                'jenis_obat'
            ]));
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('jenis-obat.index')
                    ->withErrors(['kode_jenis' => 'Kode Jenis "' . $request->kode_jenis . '" atau jenis obat sudah ada.'])
                    ->withInput()
                    ->with('edit_jenis_id', $jenis_obat->id);
            }
            throw $exception;
        }

        return redirect()
            ->route('jenis-obat.index')
            ->with('success', 'Jenis Obat berhasil diperbarui');
    }

    // 👉 DELETE
    public function destroy(JenisObat $jenis_obat)
    {
        if ($jenis_obat->namaObat()->exists()) {
            return redirect()
                ->route('jenis-obat.index')
                ->with('error', 'Jenis Obat tidak bisa dihapus karena sudah digunakan pada Daftar Obat.');
        }

        $jenis_obat->delete();

        return redirect()
            ->route('jenis-obat.index')
            ->with('success', 'Jenis Obat berhasil dihapus');
    }
}
