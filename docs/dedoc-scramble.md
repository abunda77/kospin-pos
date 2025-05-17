# Dedoc Scramble

Dedoc Scramble adalah generator dokumentasi API OpenAPI (Swagger) untuk Laravel yang modern dan otomatis. Library ini menghasilkan dokumentasi API secara otomatis tanpa perlu menulis anotasi PHPDoc secara manual, sehingga dokumentasi selalu up-to-date sesuai kode.

## Fitur Utama

-   Otomatis menghasilkan dokumentasi API dari kode Laravel
-   Mendukung format OpenAPI 3.1.0
-   Tidak memerlukan anotasi PHPDoc
-   Mendukung tampilan UI dokumentasi (Stoplight Elements)
-   Mendukung dokumentasi untuk Spatie Laravel Data & Laravel Query Builder (PRO)
-   Mendukung multi versi API, autentikasi, dan kustomisasi dokumen

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require dedoc/scramble
```

## Penggunaan Dasar

Setelah instalasi, Scramble akan menambahkan dua route baru pada aplikasi Anda:

-   `/docs/api` — UI viewer untuk dokumentasi API
-   `/docs/api.json` — File OpenAPI JSON yang mendeskripsikan API Anda

Secara default, route ini hanya tersedia pada environment `local`. Anda dapat mengubah perilaku ini dengan mendefinisikan gate `viewApiDocs`.

## Contoh Penggunaan

1. **Akses Dokumentasi API**

    - Buka browser dan akses `http://localhost:8000/docs/api` untuk melihat dokumentasi API secara interaktif.
    - Untuk mendapatkan file spesifikasi OpenAPI, akses `http://localhost:8000/docs/api.json`.

2. **Kustomisasi**

    - Anda dapat mengkustomisasi dokumentasi dengan menambahkan konfigurasi pada file `config/scramble.php` (akan dibuat otomatis setelah instalasi atau publish config).

3. **Menampilkan Dokumentasi di Environment Lain**
    - Tambahkan gate di `AuthServiceProvider`:
        ```php
        Gate::define('viewApiDocs', function ($user = null) {
            return true; // Atur sesuai kebutuhan
        });
        ```

## Dokumentasi Lengkap

Untuk dokumentasi lebih lanjut, kunjungi:

-   [scramble.dedoc.co](https://scramble.dedoc.co/)
-   [dedoc/scramble di GitHub](https://github.com/dedoc/scramble)

## Catatan

-   Library ini masih dalam tahap awal pengembangan, sehingga API dapat berubah sewaktu-waktu.
-   Untuk fitur PRO (Laravel Data & Query Builder), silakan cek dokumentasi resmi.

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Dedoc Scramble._
