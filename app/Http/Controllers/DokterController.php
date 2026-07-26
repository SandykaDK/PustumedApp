<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->get('status', 'semua');
        $sort = $request->sort ?? 'id';
        $direction = $request->direction ?? 'asc';
        $perPage = $request->per_page ?? 10;

        // whitelist allowed sortable columns
        $allowed = ['id', 'nama', 'alamat', 'jenis_kelamin', 'no_telepon', 'email', 'status', 'no_bpjs', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'id';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $dokters = Dokter::withCount('pengeluaranObat')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
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
        $messages = [
            'nama.required' => 'Nama Dokter harus diisi.',
            'nama.string' => 'Nama Dokter harus berupa teks.',
            'nama.max' => 'Nama Dokter tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama Dokter "' . $request->nama . '" sudah ada.',
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
            'jenis_kelamin.required' => 'Jenis Kelamin harus dipilih.',
            'jenis_kelamin.in' => 'Jenis Kelamin tidak valid.',
            'no_telepon.required' => 'No. Telepon harus diisi.',
            'no_telepon.string' => 'No. Telepon harus berupa teks.',
            'no_telepon.max' => 'No. Telepon tidak boleh lebih dari 255 karakter.',
            'no_telepon.unique' => 'No. Telepon "' . $request->no_telepon . '" sudah ada.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email "' . $request->email . '" sudah ada.',
            'status.required' => 'Status akun harus dipilih.',
            'status.in' => 'Status akun tidak valid.',
        ];

        $validator = Validator::make($request->all(), [
            'nama'         => 'required|string|max:255|unique:dokter,nama',
            'alamat'       => 'required|string|max:255',
            'jenis_kelamin'=> 'required|in:L,P',
            'no_telepon'   => 'required|string|max:255|unique:dokter,no_telepon',
            'email'        => 'required|email|unique:dokter,email',
            'status'       => 'required|in:aktif,nonaktif',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('dokter.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Dokter::create([
                'nama'         => $request->nama,
                'alamat'       => $request->alamat,
                'jenis_kelamin'=> $request->jenis_kelamin,
                'no_telepon'   => $request->no_telepon,
                'email'        => $request->email,
                'status'       => $request->status,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('dokter.index')
                    ->withErrors(['email' => 'Data dokter sudah ada.'])
                    ->withInput();
            }
            throw $exception;
        }

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
        $messages = [
            'nama.required' => 'Nama Dokter harus diisi.',
            'nama.string' => 'Nama Dokter harus berupa teks.',
            'nama.max' => 'Nama Dokter tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama Dokter "' . $request->nama . '" sudah ada.',
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
            'jenis_kelamin.required' => 'Jenis Kelamin harus dipilih.',
            'jenis_kelamin.in' => 'Jenis Kelamin tidak valid.',
            'no_telepon.required' => 'No. Telepon harus diisi.',
            'no_telepon.string' => 'No. Telepon harus berupa teks.',
            'no_telepon.max' => 'No. Telepon tidak boleh lebih dari 255 karakter.',
            'no_telepon.unique' => 'No. Telepon "' . $request->no_telepon . '" sudah ada.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email "' . $request->email . '" sudah ada.',
            'status.required' => 'Status akun harus dipilih.',
            'status.in' => 'Status akun tidak valid.',
        ];

        $validator = Validator::make($request->all(), [
            'nama'         => 'required|string|max:255|unique:dokter,nama,' . $dokter->id,
            'alamat'       => 'required|string|max:255',
            'jenis_kelamin'=> 'required|in:L,P',
            'no_telepon'   => 'required|string|max:255|unique:dokter,no_telepon,' . $dokter->id,
            'email'        => 'required|email|unique:dokter,email,' . $dokter->id,
            'status'       => 'required|in:aktif,nonaktif',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('dokter.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_dokter_id', $dokter->id);
        }

        try {
            $dokter->update($request->only([
                'nama',
                'alamat',
                'jenis_kelamin',
                'no_telepon',
                'email',
                'status',
            ]));
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('dokter.index')
                    ->withErrors(['email' => 'Data dokter sudah ada.'])
                    ->withInput()
                    ->with('edit_dokter_id', $dokter->id);
            }
            throw $exception;
        }

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
