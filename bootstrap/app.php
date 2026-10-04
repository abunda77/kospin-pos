<?php

use App\Http\Middleware\BlockBlacklistedIp;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            TrustProxies::class,
        ]);

        // Didaftarkan paling awal agar request dari IP blacklist tidak
        // menghabiskan kuota rate limit.
        $middleware->api(prepend: [
            BlockBlacklistedIp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Respons 429 terstandar untuk klien API.
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            $headers = $e->getHeaders();
            $retryAfter = (int) ($headers['Retry-After'] ?? 0);

            return response()->json([
                'success' => false,
                'message' => $retryAfter > 0
                    ? "Terlalu banyak permintaan. Coba lagi dalam {$retryAfter} detik."
                    : 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                'retry_after' => $retryAfter,
            ], 429, $headers);
        });
    })->create();
