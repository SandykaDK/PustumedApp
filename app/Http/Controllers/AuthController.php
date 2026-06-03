<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $identifier = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required',
        ]);

        // Cek apakah identifier adalah email atau username
        $field = filter_var($identifier['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $field => $identifier['identifier'],
            'password' => $identifier['password'],
            'status' => 'aktif',
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'identifier' => 'Email/username atau password salah.',
        ])->onlyInput('identifier');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
