<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->role !== $role) {
            return response()->json([
                'message' => 'Akses ditolak. Role tidak sesuai.'
            ], 403);
        }

        return $next($request);
    }
}