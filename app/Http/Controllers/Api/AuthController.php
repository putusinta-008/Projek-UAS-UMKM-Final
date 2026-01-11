<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register user (default role: user)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        return response()->json([
            'message' => 'Register berhasil',
            'user'    => $user
        ], 201);
    }

    /**
     * Login & generate JWT token (PAKSA GUARD API)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // 🔥 INI KUNCINYA: PAKSA GUARD API
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'message'     => 'Login berhasil',
            'token'       => $token,
            'token_type'  => 'bearer',
            'user'        => Auth::guard('api')->user()
        ]);
    }

    /**
     * Get data user yang sedang login
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Logout (invalidate token)
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Logout berhasil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal logout atau token tidak valid'
            ], 401);
        }
}


}