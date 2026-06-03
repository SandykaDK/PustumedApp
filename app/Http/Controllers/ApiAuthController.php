<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $user = User::where($field, $data['identifier'])->where('status','aktif')->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // generate plain token and store its hash
        $plain = bin2hex(random_bytes(40));
        $hash = hash('sha256', $plain);

        $token = ApiToken::create([
            'user_id' => $user->id,
            'name' => 'tab-login',
            'token' => $hash,
        ]);

        return response()->json(['token' => $plain, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        $token = $token ?: $request->header('X-Api-Token') ?: $request->query('token');
        if ($token) {
            $hash = hash('sha256', $token);
            ApiToken::where('token', $hash)->delete();
        }

        // Also logout session if present
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}
