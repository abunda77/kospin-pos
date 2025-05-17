# Laravel Sanctum

Laravel Sanctum adalah sistem autentikasi ringan untuk SPA (Single Page Application), aplikasi mobile, dan API berbasis token di Laravel. Sanctum memungkinkan setiap user untuk memiliki banyak API token dengan kemampuan (abilities) yang dapat diatur.

## Fitur Utama

-   Autentikasi API berbasis token (personal access token)
-   Autentikasi SPA berbasis cookie/session
-   Mendukung token abilities (scopes)
-   Middleware untuk proteksi route
-   Mudah diintegrasikan dengan aplikasi Laravel

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Tambahkan middleware berikut pada group `api` di `app/Http/Kernel.php` jika menggunakan SPA:

```php
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

'api' => [
    EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

## Penggunaan Dasar

### 1. API Token Authentication

Tambahkan trait `HasApiTokens` pada model User:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}
```

#### Membuat Token

```php
$token = $user->createToken('token-name');
return $token->plainTextToken;
```

#### Menggunakan Token

Kirim token pada header Authorization:

```
Authorization: Bearer {token}
```

#### Proteksi Route

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

#### Mengatur Abilities (Scopes)

```php
$user->createToken('token-name', ['server:update'])->plainTextToken;
```

#### Mengecek Ability

```php
if ($user->tokenCan('server:update')) {
    // ...
}
```

#### Revoke Token

```php
// Semua token
$user->tokens()->delete();
// Token tertentu
$user->tokens()->where('id', $tokenId)->delete();
```

### 2. SPA Authentication

-   SPA melakukan request ke `/sanctum/csrf-cookie` untuk inisialisasi CSRF
-   Lakukan login ke `/login` (menggunakan session/cookie)
-   Proteksi route dengan middleware `auth:sanctum`

## Dokumentasi Lengkap

-   [Laravel Sanctum Documentation](https://laravel.com/docs/12.x/sanctum)
-   [GitHub Laravel Sanctum](https://github.com/laravel/sanctum)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Laravel Sanctum._
