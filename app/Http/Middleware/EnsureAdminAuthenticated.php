<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        if ($user?->role !== 'admin') {
            abort(Response::HTTP_FORBIDDEN, 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}
