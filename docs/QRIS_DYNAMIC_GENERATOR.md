# QRIS Dynamic Generator - Dokumentasi

## Deskripsi
Halaman Filament untuk mengkonversi QRIS statis menjadi QRIS dinamis dengan nominal dan biaya tertentu.

## Fitur
- ✅ Pilih QRIS statis dari database atau paste manual
- ✅ Input nominal transaksi
- ✅ Tambahkan biaya (Rupiah atau Persentase)
- ✅ Generate QR code image otomatis
- ✅ Download QR code sebagai PNG
- ✅ Copy QRIS string ke clipboard
- ✅ Auto-detect nama merchant
- ✅ Terintegrasi dengan Filament Shield untuk permission

## Instalasi

### 1. Install Package QR Code
```bash
composer require endroid/qr-code
```

### 2. Buat Direktori Storage
```bash
php artisan storage:link
mkdir -p storage/app/public/qris-generated
```

### 3. File yang Dibuat
- `app/Filament/Pages/QrisDynamicGenerator.php` - Controller halaman
- `resources/views/filament/pages/qris-dynamic-generator.blade.php` - View blade

## Penggunaan

### Akses Halaman
1. Login ke admin panel Filament
2. Navigasi ke menu **Manajemen Keuangan** → **QRIS Generator**

### Generate Dynamic QRIS
1. **Pilih QRIS Statis** (opsional):
   - Pilih dari dropdown QRIS yang sudah tersimpan
   - Atau paste string QRIS statis secara manual

2. **Input Nominal**:
   - Masukkan jumlah transaksi dalam Rupiah
   - Contoh: 10000

3. **Tambah Biaya** (opsional):
   - Pilih tipe biaya: Rupiah atau Persentase
   - Masukkan nilai biaya
   - Contoh: 1000 (Rupiah) atau 2.5 (Persentase)

4. **Generate**:
   - Klik tombol "Generate Dynamic QRIS"
   - QR code akan muncul beserta string QRIS-nya

5. **Download/Copy**:
   - Download QR code sebagai gambar PNG
   - Copy QRIS string untuk integrasi API

## Struktur Navigasi
```
Manajemen Keuangan
├── QRIS Statis (sort: 5)
└── QRIS Generator (sort: 10)
```

## Permission
Halaman ini menggunakan `HasPageShield` trait dari Filament Shield.

Untuk memberikan akses:
```bash
php artisan shield:generate
```

Atau atur manual di role management:
- Permission: `page_QrisDynamicGenerator`

## Technical Details

### Algoritma Konversi
1. Remove CRC (4 karakter terakhir)
2. Ubah dari static (010211) ke dynamic (010212)
3. Split berdasarkan country code (5802ID)
4. Tambahkan tag amount (54)
5. Tambahkan tag fee jika ada (55)
6. Hitung ulang CRC16 checksum

### Tag QRIS
- **54**: Amount (nominal transaksi)
- **55**: Fee
  - **5502**: Fee indicator
  - **02**: Fixed fee (Rupiah)
  - **03**: Percentage fee
  - **56**: Fee value (Rupiah)
  - **57**: Fee value (Persentase)
- **59**: Merchant name
- **5802ID**: Country code Indonesia

### CRC16 Calculation
Menggunakan algoritma CRC-16-CCITT dengan:
- Initial value: 0xFFFF
- Polynomial: 0x1021

## Troubleshooting

### Error: Class 'Endroid\QrCode\Builder\Builder' not found
```bash
composer require endroid/qr-code
```

### QR Code tidak muncul
1. Pastikan storage sudah di-link:
   ```bash
   php artisan storage:link
   ```

2. Cek permission folder:
   ```bash
   chmod -R 775 storage/app/public/qris-generated
   ```

### QRIS tidak valid
- Pastikan string QRIS statis valid dan lengkap
- Cek format harus mengandung '5802ID' (country code Indonesia)
- Minimal panjang string harus > 4 karakter

## Contoh Output

### Input
- Static QRIS: `00020101021126...6304XXXX`
- Amount: 50000
- Fee Type: Rupiah
- Fee Value: 1000

### Output
- Dynamic QRIS: `00020101021254...6304YYYY`
- QR Image: `qris-dynamic-20241108123456-abc123.png`
- Merchant: Nama Toko

## API Integration
QRIS string yang dihasilkan dapat digunakan untuk:
- Payment gateway integration (Midtrans, Xendit, dll)
- Mobile app QR scanner
- Thermal printer receipt
- Web-based payment page

## Related Files
- `app/Models/QrisStatic.php` - Model QRIS statis
- `app/Filament/Resources/QrisStaticResource.php` - Resource QRIS statis
- `app/Helpers/QrisHelper.php` - Helper functions untuk QRIS

## Changelog
- **v1.0.0** (2024-11-08): Initial release
  - Generate dynamic QRIS dari static
  - Support fee (Rupiah & Persentase)
  - Auto-generate QR image
  - Download & copy functionality
