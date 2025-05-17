# Flowframe Laravel Trend

Flowframe Laravel Trend adalah package Laravel untuk menghasilkan data tren dari model secara mudah dan efisien. Sangat cocok untuk membuat chart, laporan, atau analisis data periodik.

## Fitur Utama

-   Menghasilkan agregasi data (count, sum, average, min, max) berdasarkan interval waktu (menit, jam, hari, bulan, tahun)
-   Mendukung query kustom dan filter Eloquent
-   Mendukung berbagai database: MySQL, MariaDB, SQLite, PostgreSQL
-   Fluent API, mudah digunakan

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require flowframe/laravel-trend
```

## Penggunaan Dasar

Import class `Flowframe\Trend\Trend` dan gunakan salah satu metode berikut:

### Contoh: Total data per bulan

```php
use Flowframe\Trend\Trend;
use App\Models\User;

$trend = Trend::model(User::class)
    ->between(
        start: now()->startOfYear(),
        end: now()->endOfYear(),
    )
    ->perMonth()
    ->count();
```

### Contoh: Rata-rata berat user per tahun dengan filter nama

```php
$trend = Trend::query(User::where('name', 'like', 'a%'))
    ->between(
        start: now()->startOfYear()->subYears(10),
        end: now()->endOfYear(),
    )
    ->perYear()
    ->average('weight');
```

### Interval yang Didukung

-   `perMinute()`
-   `perHour()`
-   `perDay()`
-   `perMonth()`
-   `perYear()`

### Agregasi yang Didukung

-   `sum('kolom')`
-   `average('kolom')`
-   `max('kolom')`
-   `min('kolom')`
-   `count('*')`

### Mengganti Kolom Tanggal

Secara default menggunakan kolom `created_at`. Untuk mengganti:

```php
Trend::model(Order::class)
    ->dateColumn('custom_date_column')
    ->between(...)
    ->perDay()
    ->count();
```

## Dokumentasi Lengkap

-   [GitHub Flowframe Laravel Trend](https://github.com/Flowframe/laravel-trend)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Flowframe Laravel Trend._
