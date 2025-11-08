# Setup QRIS Static Resource

## Instalasi Library

Untuk menggunakan fitur QRIS Static, Anda perlu menginstall library QR code reader:

```bash
composer require khanamiryan/qrcode-detector-decoder
```

## Migrasi Database

Jalankan migrasi untuk membuat tabel `qris_statics`:

```bash
php artisan migrate
```

## Struktur Tabel

Tabel `qris_statics` memiliki kolom:
- `id` - Primary key
- `name` - Nama QRIS (required)
- `qris_string` - String QRIS (required, text)
- `qris_image` - Path gambar QRIS (nullable)
- `merchant_name` - Nama merchant (nullable, auto-detected)
- `description` - Deskripsi (nullable)
- `is_active` - Status aktif (boolean, default: true)
- `created_at` - Timestamp dibuat
- `updated_at` - Timestamp diperbarui

## Fitur

### 1. Upload Gambar QRIS
- Upload gambar QR code (PNG/JPG, max 2MB)
- String QRIS akan diekstrak otomatis dari gambar
- Nama merchant akan terdeteksi otomatis

### 2. Paste String QRIS Manual
- Paste string QRIS secara langsung
- Nama merchant akan terdeteksi otomatis

### 3. View Modal
- Lihat detail QRIS dengan gambar QR code
- Salin string QRIS dengan satu klik
- Informasi lengkap merchant dan status

### 4. Filter & Search
- Filter berdasarkan status aktif/tidak aktif
- Search berdasarkan nama dan merchant

## Penggunaan

1. Buka menu **Menejemen keuangan** > **QRIS Statis**
2. Klik **Buat** untuk menambah QRIS baru
3. Upload gambar QRIS atau paste string QRIS
4. Nama merchant akan terdeteksi otomatis
5. Isi nama dan deskripsi (opsional)
6. Simpan

## Catatan

- Pastikan folder `storage/app/public/qris-images` dapat ditulis
- Jalankan `php artisan storage:link` jika belum
- Library `khanamiryan/qrcode-detector-decoder` diperlukan untuk ekstraksi QR code dari gambar
