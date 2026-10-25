# Kospin POS

Aplikasi **Point of Sale (POS)** berbasis Laravel 11 dan Filament 3 untuk koperasi/toko:
katalog publik, cart, checkout dengan pembayaran QRIS (Midtrans), kasir untuk transaksi
offline, cetak struk thermal, laporan penjualan, backup database, dan API untuk aplikasi
mobile/klien lain.

Repositori: <https://github.com/abunda77/kospin-pos>

## Fitur Utama

**Storefront publik**

- Katalog produk dengan filter kategori, pencarian, dan unduh katalog PDF
- Tampilan mobile (`/m/catalog`) dan desktop dengan preferensi tersimpan di session
  (`view_preference`)
- Cart berbasis Livewire: tambah, ubah jumlah, hapus, dan voucher diskon
- Checkout dengan Midtrans (QRIS dinamis, GoPay) dan halaman thank-you, cek status
  pembayaran realtime, serta struk PDF

**Panel admin Filament (`/admin`)**

| Menu | Keterangan |
| --- | --- |
| POS (`/admin/pos`) | Kasir: pilih produk, input barang, diskon, pembayaran, cetak struk |
| Laporan Penjualan | Rekap transaksi dan total penjualan per periode |
| Backup Database | Backup database manual (mysqldump): buat, unduh, dan hapus file backup |
| QRIS Generator | Membuat QRIS dinamis dari QRIS statis beserta riwayatnya |
| Info System | Monitoring server read-only (OS, PHP, modul PHP, Composer, database, runtime, log) |
| Settings (grup) | Grup navigasi untuk resource **Gambar** serta halaman **Backup Database** dan **Info System** |

**Resource admin**: Produk, Kategori, Voucher Diskon, Order, Metode Pembayaran, Expense,
Anggota, Banner Iklan, Gambar, QRIS Static, User, dan Setting. Hak akses diatur dengan
Filament Shield (role contoh: `super_admin`, `Kasir`, `Stok`).

**Keamanan**

- Autentikasi API dengan Sanctum; login dibatasi rate limit
- IP blacklist (`BlockBlacklistedIp`) dan throttle pada endpoint checkout
- Social login pada panel admin (opsional)
- Dokumentasi API (`/docs/api`) dilindungi Basic Auth

## Persiapan

**Peralatan**

1. Local Server (Laragon/XAMPP) dengan PHP >= 8.2
2. Composer
3. Git
4. Node.js + npm
5. MySQL (atau database lain yang didukung Laravel)

**Perangkat eksternal**

1. Printer Thermal 58mm — sambungkan ke komputer dan install drivernya terlebih dahulu agar
   struk dapat dicetak
2. Scanner QR Code (kamera atau alat scanner) — opsional, dipakai pada halaman kasir

## Setup Project

```bash
# 1. Ambil source code
git clone https://github.com/abunda77/kospin-pos.git
cd kospin-pos

# 2. Install dependency backend & frontend
composer install
npm install

# 3. Siapkan environment
cp .env.example .env      # Windows: copy .env.example .env
php artisan key:generate

# 4. Buat database, lalu sesuaikan .env (lihat tabel konfigurasi di bawah)
#    Contoh MySQL:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=kasirpos
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Migrasi struktur tabel
php artisan migrate

# 6. Symlink storage agar gambar produk dan QRIS dapat diakses publik
php artisan storage:link

# 7. Build aset frontend
npm run build             # atau: npm run dev (mode development)

# 8. Jalankan server
php artisan serve
```

Buka <http://127.0.0.1:8000> untuk storefront dan <http://127.0.0.1:8000/admin> untuk panel admin.

**Membuat akun admin pertama**

```bash
php artisan make:filament-user      # membuat user panel admin
php artisan shield:generate --all   # membuat permission untuk semua resource & page
```

Setelah user dibuat, berikan role dan permission lewat menu **Role** / **User** di panel
admin. Password default user hasil seeder (`php artisan db:seed`) adalah `password` —
segera ganti untuk lingkungan produksi.

> Catatan: halaman dan resource di panel admin dilindungi Filament Shield. Bila permission
> belum dibuat (`shield:generate`), menu tidak akan muncul di navigasi dan akses langsung
> akan ditolak. Bila permission belum sinkron setelah menambah page/resource baru, jalankan
> `php artisan shield:generate --page='App\Filament\Pages\NamaHalaman'` atau
> `php artisan shield:generate --all`.

## Konfigurasi Environment

Variabel penting di `.env` (jangan menyimpan kredensial asli di dalam repositori):

| Variabel | Keterangan |
| --- | --- |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Koneksi database |
| `APP_URL`, `APP_TIMEZONE`, `APP_LOCALE` | Identitas & lokalisasi aplikasi |
| `MIDTRANS_MERCHANT_ID`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_SERVER_KEY` | Kredensial Midtrans |
| `MIDTRANS_IS_PRODUCTION` | `true` untuk mode produksi Midtrans (default sandbox) |
| `WEBHOOK_URL` | Endpoint notifikasi order; bila diisi, `OrderObserver` mengirim notifikasi |
| `SCRAMBLE_DOCS_PASSWORD` | Password Basic Auth `/docs/api`; bila kosong, dokumentasi diblokir 403 |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_CALLBACK` | Social login Google (opsional) |
| `SECURITY_BLACKLIST_PATH` | Lokasi file daftar IP blacklist (default `blacklist.txt`) |
| `FILESYSTEM_DISK`, `QUEUE_CONNECTION`, `CACHE_STORE`, `SESSION_DRIVER` | Driver penyimpanan, antrian, cache, dan session |

Gateway pembayaran dipilih lewat `PaymentGatewayFactory` dengan key `midtrans` atau `xendit`
(konfigurasi di `config/services.php`). Tambahkan kredensial Xendit bila gateway tersebut
digunakan.

## URL Penting

| URL | Keterangan |
| --- | --- |
| `/` | Katalog produk (desktop) |
| `/m/catalog` | Katalog produk (mobile) |
| `/cart`, `/checkout`, `/thank-you/{order}` | Cart, checkout, dan halaman terima kasih |
| `/struk/{orderId}` | Struk transaksi |
| `/about`, `/contact`, `/info` | Halaman statis |
| `/admin` | Panel admin Filament |
| `/admin/pos` | Halaman kasir |
| `/admin/info-system` | Monitoring server |
| `/docs/api` | Dokumentasi API (Scramble, Basic Auth) |
| `/up` | Health check |
| `/api/*` | REST API (Sanctum), webhook Midtrans di `POST /api/midtrans/notification` |

## Menjalankan Aplikasi

```bash
php artisan serve            # server development (port 8000)
npm run dev                  # Vite dev server (hot reload)
npm run build                # build aset produksi
php artisan optimize         # cache config, route, view, event (produksi)
```

Untuk performa lebih tinggi aplikasi dapat dijalankan dengan Laravel Octane; lihat
`docs/laravel-octane.md`. Aplikasi juga harus aman dijalankan dengan Octane: jangan
menyimpan state per-request pada singleton atau properti statis.

## Testing & Formatting

```bash
# Semua test
php artisan test --compact

# Test tertentu
php artisan test --compact tests/Feature/InfoSystemPageTest.php
php artisan test --compact --filter=namaTest

# Format kode PHP (Pint)
vendor/bin/pint --dirty --format agent
```

Test menggunakan database yang dikonfigurasi pada `.env` (override SQLite di `phpunit.xml`
sengaja dinonaktifkan) dan beberapa test memakai `RefreshDatabase`. Karena itu **arahkan
`DB_DATABASE` ke database sekali pakai** sebelum menjalankan seluruh test, atau override saat
menjalankan perintah, contoh:

```bash
DB_CONNECTION=sqlite DB_DATABASE=/tmp/kospin-test.sqlite php artisan test --compact
```

## Teknologi dan Library

### Backend

- PHP ^8.2
- Laravel Framework ^11.9
- Laravel Filament ^3.2 (Admin Panel)
- Laravel Sanctum ^4.0 (API Authentication)
- Laravel Octane ^2.6 (Performance)
- Spatie Laravel Permission ^6.10 (Role & Permission)
- Bezhansalleh Filament Shield ^3.3 (Admin Panel Security)
- TomatoPHP Filament Social ^1.0 (Social Login)
- Barryvdh Laravel DomPDF ^3.1 (PDF Generator)
- Mike42 ESC/POS PHP ^4.0 (Thermal Printer)
- Picqer PHP Barcode Generator ^3.2
- Spatie Laravel Backup ^9.2
- Maatwebsite Excel ^3.1 (Excel Import/Export)
- Intervention Image ^3.11 (Image Processing)
- Dedoc Scramble ^0.11.33 (API Documentation)
- Flowframe Laravel Trend ^0.3.0 (Data Trending)
- Doctrine DBAL ^3.0 (Database Abstraction Layer)
- Endroid QR Code ^6.0 (QRIS)
- Laravel Socialite ^5.0 (via Filament Social)
- Xendit PHP SDK ^7.0 (Payment Gateway)
- Midtrans PHP SDK ^2.6 (Payment Gateway)

### Frontend

- TailwindCSS 3.4.1
- Vite 5.0
- Alpine.js 3.14
- Axios 1.6.4
- PostCSS 8.4.33
- Autoprefixer 10.4.17

## Model yang Digunakan

- Product - Pengelolaan produk/barang
- Category - Kategori produk
- Order - Transaksi penjualan (primary key UUID, nomor order dibuat dengan
  `Order::generateNextOrderNumber()` di dalam transaksi)
- OrderProduct - Detail produk dalam transaksi
- PaymentMethod - Metode pembayaran
- VoucherDiskon - Pengelolaan voucher dan diskon
- BannerIklan - Pengelolaan banner iklan
- Anggota - Data anggota/member
- User - Pengguna sistem
- Expense - Pengeluaran
- Quote - Kutipan untuk widget dashboard
- BackupLog - Log pencadangan data
- Setting - Pengaturan aplikasi
- Image - Pengelolaan gambar
- QrisStatic - Data QRIS statis (sumber QRIS dinamis)
- QrisDynamic - Riwayat QRIS dinamis yang dibuat dari panel admin

## Struktur Project

```text
app/
  Filament/
    Pages/        # PosPage, LaporanPenjualan, BackupDatabase, QrisDynamicGenerator, InfoSystem
    Resources/    # CRUD admin (Product, Order, Anggota, Setting, dll)
    Widgets/      # Widget dashboard + grafik Info System
  Http/
    Controllers/  # Controllers web (Catalog, Cart, Checkout, Struk, ...) dan Api/
    Middleware/   # BlockBlacklistedIp, SecureDocsAccess, DetectMobileDevice, TrustProxies, ...
  Livewire/       # Komponen storefront & POS (CartItems, AddToCart, Scanner, ...)
  Models/         # Model Eloquent
  Services/Payment/  # PaymentGatewayFactory, MidtransGateway, XenditGateway
  Support/        # SystemInfo (probe monitoring server)
routes/
  web.php, api.php, console.php
resources/views/  # Blade storefront, panel admin, PDF, struk
```

## Dokumentasi Tambahan

- `CHANGELOG.md` — riwayat perubahan per periode
- `AGENTS.md` — konvensi dan aturan kontribusi kode
- `INFO_SYSTEM_PAGE.md` — dokumentasi halaman Info System
- `API_SECURITY.md` — keamanan API (blacklist IP, rate limit)
- `docs/` — catatan integrasi library (Filament, Livewire, Sanctum, Octane, Scramble, dll)
- `QRIS_*.md`, `GOPAY_*.md` — alur dan troubleshooting pembayaran QRIS/GoPay
