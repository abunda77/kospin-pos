# QRIS Static Resource

Resource Filament untuk mengelola QRIS statis dalam sistem POS.

## Fitur Utama

### 1. Upload & Auto-Extract
- Upload gambar QR code QRIS (PNG/JPG)
- Ekstraksi otomatis string QRIS dari gambar
- Deteksi otomatis nama merchant

### 2. Input Manual
- Paste string QRIS secara langsung
- Auto-detect nama merchant dari string

### 3. Manajemen
- Aktifkan/nonaktifkan QRIS
- Filter berdasarkan status
- Search berdasarkan nama dan merchant

### 4. View Detail
- Modal view dengan gambar QR code
- Copy string QRIS dengan satu klik
- Informasi lengkap merchant

## Instalasi

### 1. Install Library QR Code Reader

```bash
composer require khanamiryan/qrcode-detector-decoder
```

### 2. Jalankan Migrasi

```bash
php artisan migrate
```

### 3. Link Storage (jika belum)

```bash
php artisan storage:link
```

## Struktur File

```
app/Filament/Resources/QrisStaticResource/
├── Pages/
│   ├── CreateQrisStatic.php    # Halaman create
│   ├── EditQrisStatic.php      # Halaman edit
│   └── ListQrisStatics.php     # Halaman list
└── README.md                    # File ini

app/Helpers/
└── QrisHelper.php               # Helper untuk ekstraksi QRIS

app/Models/
└── QrisStatic.php               # Model QRIS Static

resources/views/filament/resources/qris-static/
└── view-modal.blade.php         # View modal detail

database/migrations/
└── xxxx_create_qris_statics_table.php  # Migration
```

## Penggunaan

### Menambah QRIS Baru

1. Buka **Menejemen keuangan** > **QRIS Statis**
2. Klik tombol **Buat**
3. Isi form:
   - **Nama**: Nama identifikasi QRIS (required)
   - **Upload Gambar QRIS**: Upload gambar QR code (opsional)
   - **String QRIS**: Paste string QRIS atau biarkan auto-fill dari upload
   - **Nama Merchant**: Auto-terisi dari QRIS
   - **Deskripsi**: Catatan tambahan (opsional)
   - **Aktif**: Toggle status aktif
4. Klik **Simpan**

### Melihat Detail QRIS

1. Pada tabel list, klik icon **mata** pada baris QRIS
2. Modal akan menampilkan:
   - Gambar QR code (jika ada)
   - Detail lengkap (nama, merchant, status)
   - String QRIS dengan tombol copy

### Edit QRIS

1. Klik icon **pensil** pada baris QRIS
2. Edit field yang diperlukan
3. Klik **Simpan**

### Filter & Search

- **Filter Status**: Gunakan filter "Status Aktif" untuk melihat QRIS aktif/tidak aktif
- **Search**: Ketik di search box untuk mencari berdasarkan nama atau merchant

## Validasi QRIS

QrisHelper melakukan validasi dasar:
- Panjang string minimal 50 karakter
- Harus mengandung tag `5802ID` (Indonesia)
- Harus mengandung tag `0002` (QRIS version)

## Troubleshooting

### Gambar tidak muncul
- Pastikan `php artisan storage:link` sudah dijalankan
- Cek permission folder `storage/app/public/qris-images`

### Ekstraksi QRIS gagal
- Pastikan gambar QR code jelas dan tidak blur
- Ukuran gambar tidak terlalu kecil (minimal 200x200px)
- Format gambar PNG/JPG

### Library tidak ditemukan
```bash
composer require khanamiryan/qrcode-detector-decoder
```

## Navigation

- **Group**: Menejemen keuangan
- **Label**: QRIS Statis
- **Icon**: heroicon-o-qr-code
- **Sort**: 5

## Model Attributes

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Nama QRIS |
| qris_string | text | Yes | String QRIS |
| qris_image | string | No | Path gambar |
| merchant_name | string | No | Nama merchant |
| description | text | No | Deskripsi |
| is_active | boolean | No | Status aktif (default: true) |

## Scope

- `active()`: Filter QRIS yang aktif

```php
QrisStatic::active()->get();
```

## Accessor

- `qris_image_url`: Full URL gambar QRIS

```php
$qris->qris_image_url; // https://domain.com/storage/qris-images/xxx.png
```
