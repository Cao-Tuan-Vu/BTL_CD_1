<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('customer.login'));
        }

        if (Auth::user()?->role !== 'customer') {
            abort(Response::HTTP_FORBIDDEN, 'Bạn không có quyền truy cập khu vực khách hàng.');
        }

        return $next($request);
    }
}
