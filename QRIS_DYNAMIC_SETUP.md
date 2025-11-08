# Setup QRIS Dynamic Generator

## Quick Install

```bash
# 1. Install QR Code package
composer require endroid/qr-code

# 2. Setup storage
php artisan storage:link
mkdir storage/app/public/qris-generated

# 3. Generate permissions (jika menggunakan Shield)
php artisan shield:generate

# 4. Clear cache
php artisan optimize:clear
```

## Akses
- Menu: **Manajemen Keuangan** → **QRIS Generator**
- URL: `/admin/qris-dynamic-generator`

## Cara Pakai
1. Pilih/paste QRIS statis
2. Input nominal (Rp)
3. Tambah fee (opsional)
4. Klik "Generate Dynamic QRIS"
5. Download QR atau copy string

## Files Created
- ✅ `app/Filament/Pages/QrisDynamicGenerator.php`
- ✅ `resources/views/filament/pages/qris-dynamic-generator.blade.php`
- ✅ `docs/QRIS_DYNAMIC_GENERATOR.md`

## Navigation Group
Kedua resource QRIS sekarang dalam grup **"Manajemen Keuangan"**:
- QRIS Statis (sort: 5)
- QRIS Generator (sort: 10)

## Dokumentasi Lengkap
Lihat: `docs/QRIS_DYNAMIC_GENERATOR.md`
