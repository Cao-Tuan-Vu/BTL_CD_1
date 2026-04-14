<?php

use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\EnsureApiAdminAuthenticated;
use App\Http\Middleware\EnsureCustomerAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => EnsureAdminAuthenticated::class,
            'api.admin' => EnsureApiAdminAuthenticated::class,
            'customer.auth' => EnsureCustomerAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $renderCsrfExpired = function (Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.',
                ], 419);
            }

            return redirect($request->headers->get('referer') ?: url('/'))
                ->withInput($request->except('_token'))
                ->withErrors([
                    'session' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang rồi gửi lại biểu mẫu.',
                ]);
        };

        $exceptions->render(function (TokenMismatchException $exception, Request $request) use ($renderCsrfExpired) {
            return $renderCsrfExpired($request);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) use ($renderCsrfExpired) {
            if ($exception->getStatusCode() !== 419) {
                return null;
            }

            return $renderCsrfExpired($request);
        });
    })->create();
