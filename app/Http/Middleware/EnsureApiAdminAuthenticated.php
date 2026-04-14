<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Chưa xác thực.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (Auth::user()?->role !== 'admin') {
            return response()->json([
                'message' => 'Bạn không có quyền. Yêu cầu vai trò quản trị.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
