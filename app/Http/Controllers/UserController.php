<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $perPage = $request->per_page ?? 10;

        // Whitelist allowed sortable columns to prevent SQL injection
        $allowed = ['name', 'email', 'no_telepon', 'role', 'created_at'];
        if (! in_array($sort, $allowed)) {
            $sort = 'created_at';
        }

        // Sanitize direction
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $status = $request->status;

        $users = User::when($search, function ($query) use ($search) {
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
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'no_telepon'            => 'nullable|string|max:20',
            'role'                  => 'required|in:petugas_administrasi,petugas_obat,kepala_pustu',
            'status'                => 'required|in:aktif,nonaktif',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'no_telepon' => $request->no_telepon,
            'role'       => $request->role,
            'status'     => $request->status ?? 'aktif',
            'password'   => Hash::make($request->password),
        ]);

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
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'no_telepon'  => 'nullable|string|max:20',
            'role'        => 'required|in:petugas_administrasi,petugas_obat,kepala_pustu',
            'status'      => 'required|in:aktif,nonaktif'
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'nullable|min:6|confirmed';
            $rules['password_confirmation'] = 'required_with:password|same:password';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

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

        $user->update($data);

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

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
