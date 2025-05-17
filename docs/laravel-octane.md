# Laravel Octane

Laravel Octane adalah package untuk meningkatkan performa aplikasi Laravel dengan menjalankan aplikasi menggunakan application server berperforma tinggi seperti Swoole, Open Swoole, RoadRunner, dan FrankenPHP. Octane membuat aplikasi tetap berada di memori dan melayani request dengan sangat cepat.

## Fitur Utama

-   Menjalankan aplikasi Laravel dengan server Swoole, Open Swoole, RoadRunner, atau FrankenPHP
-   Mendukung concurrent tasks, ticks, intervals, dan cache super cepat (khusus Swoole)
-   Otomatis reload worker saat file berubah (hot reload)
-   Konfigurasi jumlah worker dan max request
-   Mendukung custom table (Swoole Table)

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require laravel/octane
php artisan octane:install
```

Pilih server yang ingin digunakan (Swoole, Open Swoole, RoadRunner, FrankenPHP) dan pastikan ekstensi PHP terkait sudah terpasang.

## Menjalankan Octane

Jalankan server Octane dengan perintah:

```bash
php artisan octane:start
```

Secara default akan berjalan di http://localhost:8000

### Menentukan Jumlah Worker

```bash
php artisan octane:start --workers=4
```

### Hot Reload Saat File Berubah

```bash
php artisan octane:start --watch
npm install --save-dev chokidar
```

### Reload Worker Setelah Deploy

```bash
php artisan octane:reload
```

### Stop Server

```bash
php artisan octane:stop
```

## Contoh Fitur Lanjutan (Swoole)

### Concurrent Tasks

```php
use Laravel\Octane\Facades\Octane;

[$users, $servers] = Octane::concurrently([
    fn () => User::all(),
    fn () => Server::all(),
]);
```

### Octane Cache

```php
Cache::store('octane')->put('framework', 'Laravel', 30);
```

### Custom Table

```php
Octane::table('example')->set('uuid', [
    'name' => 'Nuno Maduro',
    'votes' => 1000,
]);

return Octane::table('example')->get('uuid');
```

## Dokumentasi Lengkap

-   [Laravel Octane Documentation](https://laravel.com/docs/12.x/octane)
-   [GitHub Laravel Octane](https://github.com/laravel/octane)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Laravel Octane._
