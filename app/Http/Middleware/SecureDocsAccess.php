<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureDocsAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->passwordIsConfigured()) {
            // Konfigurasi hilang: jangan pernah buka dokumentasi tanpa proteksi.
            return response()->make('API docs password is not configured.', 403);
        }

        if (! $request->hasHeader('PHP_AUTH_USER') || ! $request->hasHeader('PHP_AUTH_PW')) {
            return $this->unauthorized('');
        }

        if (! hash_equals($this->password(), (string) $request->header('PHP_AUTH_PW'))) {
            return $this->unauthorized('Invalid credentials');
        }

        return $next($request);
    }

    private function password(): string
    {
        // config() agar tetap bekerja setelah `php artisan config:cache`.
        return (string) config('scramble.docs_password');
    }

    private function passwordIsConfigured(): bool
    {
        return $this->password() !== '';
    }

    private function unauthorized(string $body): Response
    {
        return response()->make($body, 401, [
            'WWW-Authenticate' => 'Basic realm="API Documentation"',
        ]);
    }
}
