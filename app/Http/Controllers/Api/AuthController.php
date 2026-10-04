<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BlacklistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;          // per email|ip

    private const MAX_LOGIN_ATTEMPTS_PER_IP = 20;  // jaring per IP

    private const LOGIN_LOCKOUT_SECONDS = 300;     // 5 menit

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->ensureEmailDomainNotBlacklisted($validatedData['email']);

        $emailKey = 'api-login:'.Str::lower($validatedData['email']).'|'.$request->ip();
        $ipKey = 'api-login:ip:'.$request->ip();

        if (RateLimiter::tooManyAttempts($emailKey, self::MAX_LOGIN_ATTEMPTS)
            || RateLimiter::tooManyAttempts($ipKey, self::MAX_LOGIN_ATTEMPTS_PER_IP)) {
            $seconds = max(1, RateLimiter::availableIn($emailKey), RateLimiter::availableIn($ipKey));

            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan login. Coba lagi dalam '.ceil($seconds / 60).' menit.',
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', (string) $seconds);
        }

        $user = User::where('email', $validatedData['email'])->first();

        if (! $user || ! Hash::check($validatedData['password'], $user->password)) {
            RateLimiter::hit($emailKey, self::LOGIN_LOCKOUT_SECONDS);
            RateLimiter::hit($ipKey, self::LOGIN_LOCKOUT_SECONDS);

            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
                'data' => null,
            ], 422);
        }

        RateLimiter::clear($emailKey);
        RateLimiter::clear($ipKey);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Sukses login',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ]);
    }

    /**
     * Cek blacklist domain di luar array rules: Scramble gagal mengevaluasi
     * rules yang memanggil method secara statis.
     */
    private function ensureEmailDomainNotBlacklisted(string $email): void
    {
        if (app(BlacklistService::class)->isBlacklistedEmailDomain($email)) {
            throw ValidationException::withMessages([
                'email' => ['Unknown occurs'],
            ]);
        }
    }
}
