# Changelog

Semua perubahan penting pada project **Kospin POS** didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.1.0/) dan penomoran
mengikuti [Semantic Versioning](https://semver.org/lang/id/). Project ini belum memakai
git tag rilis, sehingga tiap versi ditandai dengan periode bulan (`YYYY-MM`) beserta
tanggal commit terakhir pada periode tersebut. Entri disusun dari `git log`
(200 commit, 9 Januari 2025 – 25 Oktober 2026) dan commit berulang untuk satu perubahan
yang sama digabung menjadi satu baris.

Kategori yang dipakai: `Added`, `Changed`, `Fixed`, dan `Security`; perubahan dokumentasi
dicatat pada kategori yang sesuai.

## [Unreleased]

### Added

- Halaman admin **Info System** (`/admin/info-system`) di grup navigasi **Settings**: monitoring
  server read-only dengan 6 tab (Ringkasan, Sistem Operasi, PHP, Modul PHP, Composer,
  Runtime & Infra), 9 kartu ringkasan, tombol Refresh, dan auto-refresh 30 detik.
- Tiga widget grafik pada tab Ringkasan: doughnut RAM fisik, doughnut kapasitas disk, dan bar
  jumlah modul PHP per kategori.
- `app/Support/SystemInfo.php` — seluruh probe OS/PHP/database/disk/RAM/Composer/log,
  dipisah dari UI agar bisa dipakai ulang oleh widget, command, atau endpoint lain. Data
  perangkat keras di-cache satu jam (`system-info:hardware`).
- Partial Blade `info-rows` (tabel key-value) dan `data-table` (tabel Alpine dengan
  pencarian, sort, dan pagination client-side tanpa request ke server).
- Feature test `tests/Feature/InfoSystemPageTest.php` (8 test, 159 assertion).
- `CHANGELOG.md` ini.

### Changed

- `config/filament-shield.php`: key `permission_prefixes.pages` diganti menjadi
  `permission_prefixes.page` agar sesuai dengan Shield 3.9.10. Sebelumnya prefix permission
  halaman kosong sehingga `shield:generate` menghasilkan nama seperti `_InfoSystem`
  alih-alih `page_InfoSystem`.
- Permission `page_InfoSystem` ditambahkan ke database dan diberikan ke role `super_admin`.

### Security

- Halaman Info System tidak pernah memanggil `phpinfo()`; hanya nilai konfigurasi terpilih yang
  ditampilkan (`APP_KEY`, kredensial database, dan isi `.env` tidak terbaca). Karena tetap
  menampilkan hostname, IP server, dan path aplikasi, akses dibatasi lewat permission Shield.

## [2026-10] - 2026-10-25

### Added

- Pengamanan API: middleware `BlockBlacklistedIp`, rate limit login dan checkout, respons 429
  terstandar dalam bentuk JSON, serta dokumentasi `API_SECURITY.md`.
- Dokumentasi `INFO_SYSTEM_PAGE.md` sebagai acuan halaman monitoring server.

### Changed

- Registrasi mandiri pada panel admin dinonaktifkan (`->registration(false)`).
- Update dependency Composer (termasuk aset Filament di `public/`).

### Fixed

- Dokumentasi Scramble: password Basic Auth untuk `/docs/api` dibaca lewat config
  (`SCRAMBLE_DOCS_PASSWORD`), bukan `env()` langsung, sehingga tetap bekerja saat config
  di-cache. `/docs/api` diblokir 403 bila password kosong.

## [2026-08] - 2026-08-03

### Added

- Dokumentasi `MidtransService.md` (termasuk catatan discount price) dan `PaymentQRcompleted.md`.
- Input discount price pada halaman kasir (POS Livewire).

### Fixed

- Penanganan notifikasi pembayaran dari status `processing` menjadi `completed` pada webhook
  Midtrans (5 commit iteratif).

## [2026-07] - 2026-07-15

### Added

- `laravel/boost` sebagai dependency development beserta integrasi MCP Laravel Boost.
- `CLAUDE.md` dan `AGENTS.md` (panduan kontribusi untuk AI coding agent).

### Changed

- Penyesuaian `.gitignore` dan aturan agent.

## [2026-06] - 2026-06-26

### Added

- Halaman publik `/info` (`InfoController`) untuk menampilkan informasi toko.
- `POINTREWARD.md` dan pembaruan dokumentasi keamanan.

### Changed

- Pembaruan alamat, kontak, nomor telepon, dan logo toko pada halaman publik.
- Update `composer.lock` dan `package-lock.json` (perbaikan keamanan dependency).

## [2026-01] - 2026-01-01

### Added

- Tombol cek status pembayaran pada halaman checkout.
- Pemeriksaan keamanan tambahan pada alur pembayaran.

### Changed

- Update dependency npm dan Composer.

## [2025-12] - 2025-12-30

### Added

- Pengecekan status pembayaran realtime pada halaman thank-you.
- Migrasi untuk memperpanjang kolom `no_order` pada tabel `orders`.

### Fixed

- Duplikasi nomor order (`1062 Duplicate entry '000001'`) diperbaiki di level model dengan
  pembuatan nomor order di dalam transaksi dan `SELECT ... FOR UPDATE`
  (`Order::generateNextOrderNumber()`).
- Status pembayaran `pending`, tipe pembayaran GoPay, dan URL notifikasi Midtrans.
- Halaman checkout QRIS dan halaman thank-you.
- Label tombol cek status pembayaran.
- File utilitas `signkey.php` sempat ditambahkan lalu dihapus kembali.

## [2025-11] - 2025-11-26

### Added

- Social login pada panel admin (`TomatoPHP/FilamentSocial`).
- Resource **QRIS Static** untuk menyimpan QRIS statis.
- Halaman **QRIS Dynamic Generator** beserta tabel riwayat QRIS dinamis (generate, unduh,
  lihat detail, hapus, dan bulk delete).
- Aturan/steering agent (`.agent/rules`, `.kiro/steering`) untuk dokumentasi internal.

### Fixed

- Aktivasi GoPay dan QRIS GoPay pada Midtrans, daftar bank pada checkout, notifikasi Midtrans
  saat checkout, dan QRIS pada frontend.
- `Auth::id()` pada pembuatan QRIS dinamis agar kolom `created_by` terisi benar.

## [2025-07] - 2025-07-27

### Added

- Katalog versi mobile (`/m/catalog`, `/m/catalog/{category}`) beserta kartu produk, filter
  kategori, dan tombol add-to-cart mobile.
- Payment method berbasis gateway (`PaymentGatewayFactory` + `PaymentGatewayInterface` untuk
  Midtrans dan Xendit).

### Changed

- `no_order` dibuat lebih pendek dan mudah dibaca manusia.
- Pemilihan tampilan mobile/desktop disimpan di session (`view_preference`).
- Pembaruan dokumentasi pembayaran dan `README`.

### Fixed

- Nomor order yang terlalu panjang, form anggota/non-anggota, warna dan font kartu kategori,
  layout info stok pada halaman katalog, pagination katalog, dan route mode desktop/mobile.
- Dokumentasi API Scramble.

## [2025-06] - 2025-06-27

### Added

- Halaman publik **Contact** dan **About**.

## [2025-05] - 2025-05-22

### Added

- `OrderObserver` untuk notifikasi `WEBHOOK_URL` saat order dibuat.
- Dokumentasi project beserta aturan kontribusi.

### Changed

- Form input barang pada halaman kasir dan tampilan struk.
- Perbaikan tampilan `created_at` order.

### Fixed

- Pagination pada halaman kasir.

## [2025-02] - 2025-02-27

### Added

- Kompresi/optimasi gambar produk dan banner iklan (upload + resize, 15 commit iteratif).
- Endpoint API kategori dan produk publik, serta pencarian katalog berbasis query string
  (`?search=`).
- Widget stok realtime pada dashboard, kasir, katalog, dan cart.
- Widget **Kata Mutiara** (quote) beserta model `Quote` dan migrasi `quotes`.

### Changed

- Styling pagination dan badge stok produk.
- Layout katalog versi mobile dan unduhan katalog PDF.
- Komponen pencarian katalog berbasis Livewire dihapus dan diganti form pencarian pada
  halaman katalog.

### Fixed

- Pagination katalog per kategori, batas 50 item pada PDF, unduhan katalog tanpa kategori,
  dan link kategori.

## [2025-01] - 2025-01-31

### Added

- Inisialisasi project Laravel 11 + Filament 3.
- Katalog frontend dan halaman detail kategori.
- Cart berbasis Livewire (tambah, hapus, jumlah item).
- Checkout, halaman thank-you, struk PDF (juga untuk cetak ulang) dan barcode anggota.
- Laporan penjualan, diskon, dan voucher diskon.
- Resource **Anggota**, **Banner Iklan**, dan **Image** (upload gambar dengan logging error).
- Backup database via `spatie/laravel-backup` dan halaman **Backup Database** di panel admin.
- Konfigurasi proxy (`TrustProxies`) dan penyesuaian `.env` per environment.
- UUID pada model utama.

### Changed

- Struktur provider, middleware, dan konfigurasi Livewire.
- Restock quantity otomatis saat item order dihapus.
- Thumbnail kategori, banner iklan, dan logo pada struk.

### Fixed

- Cetak ulang struk, tombol produk kosong, dan tampilan logo struk.
