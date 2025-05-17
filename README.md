## Persiapan Project Kasir

1. Local Server Laragon
2. Composer
3. Git
4. Node.js
5. php version >= 8.2

_Sisi eksternal_

1. Printer Thermal ukuran 58mm (Sambungkan printer ke komputer/laptop, jika belum terdaftar pada komputer/laptop maka install driver printer terlebih dahulu atau tonton video tutorial di youtube terkait masalah ini)
2. Scanner QR Code dengan Kameran maupun alat scanner (Opsional)

## Setup Project Kasir

Sangat untuk menjalankan atau mensetup project ini.

1. Buat database terlebih dahulu
   _silahkan import file sql yang ada di dalam project ini_
2. Konfigurasikan file .env dengan database yang telah dibuat
3. Buka terminal di direktori project

4. Jalankan perintah `php artisan storage:link`
5. Jalankan perintah `php artisan serve` untuk menjalankan server
6. Buka browser dan kunjungi link http://127.0.0.1:8000
7. Login dengan email (admin@gmail.com) dan password (password)

Aplikasi siap di gunakan....

## Teknologi dan Library

### Backend

-   PHP 8.2+
-   Laravel Framework 11.9
-   Laravel Filament 3.2 (Admin Panel)
-   Laravel Sanctum 4.0 (API Authentication)
-   Laravel Octane 2.6 (Performance)
-   Spatie Laravel Permission 6.10 (Role & Permission)
-   Bezhansalleh Filament Shield 3.3 (Admin Panel Security)
-   Barryvdh Laravel DomPDF 3.1 (PDF Generator)
-   Mike42 ESC/POS PHP 4.0 (Thermal Printer)
-   Picqer PHP Barcode Generator 3.2
-   Spatie Laravel Backup 9.2
-   Maatwebsite Excel 3.1 (Excel Import/Export)
-   Intervention Image 3.11 (Image Processing)
-   Dedoc Scramble 0.11.33 (API Documentation)
-   Flowframe Laravel Trend 0.3.0 (Data Trending)
-   Doctrine DBAL 3.0 (Database Abstraction Layer)

### Frontend

-   TailwindCSS 3.4.1
-   Vite 5.0
-   Axios 1.6.4
-   PostCSS 8.4.33
-   Autoprefixer 10.4.17

## Model yang Digunakan

-   Product - Pengelolaan produk/barang
-   Category - Kategori produk
-   Order - Transaksi penjualan
-   OrderProduct - Detail produk dalam transaksi
-   PaymentMethod - Metode pembayaran
-   VoucherDiskon - Pengelolaan voucher dan diskon
-   BannerIklan - Pengelolaan banner iklan
-   Anggota - Data anggota/member
-   User - Pengguna sistem
-   Expense - Pengeluaran
-   Quote - Kutipan
-   BackupLog - Log pencadangan data
-   Setting - Pengaturan aplikasi
-   Image - Pengelolaan gambar
