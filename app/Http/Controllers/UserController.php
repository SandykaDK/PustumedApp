<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'id';
        $direction = $request->direction ?? 'asc';
        $perPage = $request->per_page ?? 10;

        // Whitelist allowed sortable columns to prevent SQL injection
        $allowed = ['id', 'name', 'email', 'no_telepon', 'role', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'id';
        }

        // Sanitize direction
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $status = $request->status;

        $users = User::withCount(['penerimaanObat', 'pengeluaranObat', 'pemusnahanObat'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('no_telepon', 'like', "%$search%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage);

        return view('daftar_user.daftar_user', compact(
            'users',
            'search',
            'sort',
            'direction',
            'perPage',
            'status'
        ));
    }

    public function create()
    {
        // Redirect to index since creating is handled via modal on the index page
        return redirect()->route('users.index');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name'                  => 'required|string|max:255|unique:users,name',
            'email'                 => 'required|email|unique:users,email',
            'no_telepon'            => 'required|string|max:20|unique:users,no_telepon',
            'role'                  => 'required|in:petugas_administrasi,petugas_obat,kepala_pustu,super_admin',
            'status'                => 'required|in:aktif,nonaktif',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama pengguna "' . $request->name . '" sudah digunakan.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email "' . $request->email . '" sudah digunakan.',
            'no_telepon.required' => 'No telepon harus diisi.',
            'no_telepon.string' => 'No telepon harus berupa teks.',
            'no_telepon.max' => 'No telepon tidak boleh lebih dari 20 karakter.',
            'no_telepon.unique' => 'No. telepon "' . $request->no_telepon . '" sudah digunakan.',
            'role.required' => 'Role harus dipilih.',
            'role.in' => 'Role tidak valid.',
            'status.required' => 'Status akun harus dipilih.',
            'status.in' => 'Status akun tidak valid.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required_with' => 'Konfirmasi password harus diisi ketika password diisi.',
            'password_confirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'no_telepon' => $request->no_telepon,
                'role'       => $request->role,
                'status'     => $request->status ?? 'aktif',
                'password'   => Hash::make($request->password),
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('users.index')
                    ->withErrors(['email' => 'Email "' . $request->email . '" sudah digunakan.'])
                    ->withInput();
            }
            throw $exception;
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('daftar_user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'        => 'required|string|max:255|unique:users,name,' . $user->id,
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'no_telepon'  => 'required|string|max:20|unique:users,no_telepon,' . $user->id,
            'role'        => 'required|in:petugas_administrasi,petugas_obat,kepala_pustu,super_admin',
            'status'      => 'required|in:aktif,nonaktif'
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'nullable|min:6|confirmed';
            $rules['password_confirmation'] = 'required_with:password|same:password';
        }

        $messages = [
            'name.unique' => 'Nama pengguna "' . $request->name . '" sudah digunakan.',
            'email.unique' => 'Email "' . $request->email . '" sudah digunakan.',
            'no_telepon.unique' => 'No. telepon "' . $request->no_telepon . '" sudah digunakan.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_user_id', $user->id);
        }

        $data = $request->only([
            'name',
            'email',
            'no_telepon',
            'role',
            'status'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        try {
            $user->update($data);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return redirect()->route('users.index')
                    ->withErrors(['email' => 'Email "' . $request->email . '" sudah digunakan.'])
                    ->withInput()
                    ->with('edit_user_id', $user->id);
            }
            throw $exception;
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    // 👉 DELETE
    public function destroy(User $user)
    {
        // Optional: cegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        if ($user->hasTransactions()) {
            return back()->with('error', 'User tidak dapat dihapus karena sudah tercatat dalam transaksi.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
