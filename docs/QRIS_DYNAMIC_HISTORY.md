# QRIS Dynamic History Feature

## Overview
Fitur ini menambahkan kemampuan untuk menyimpan dan mengelola history QRIS yang telah digenerate secara dynamic.

## Features

### 1. Automatic Saving
Setiap QRIS yang digenerate akan otomatis tersimpan ke database dengan informasi:
- Merchant name
- Amount (jumlah transaksi)
- Fee type dan value
- QR code image
- Source QRIS (jika menggunakan saved QRIS)
- User yang membuat

### 2. History Table
Tabel history menampilkan semua QRIS yang pernah digenerate dengan kolom:
- **Merchant**: Nama merchant dari QRIS
- **Amount**: Jumlah transaksi dalam Rupiah
- **Fee Type**: Jenis fee (Rupiah/Persentase)
- **Fee Value**: Nilai fee
- **Source QRIS**: QRIS static yang digunakan (jika ada)
- **Created By**: User yang membuat
- **Generated At**: Waktu pembuatan

### 3. Actions

#### Download
- Download QR code image dalam format PNG
- Nama file: `qris-dynamic-{id}-{timestamp}.png`

#### View
- Melihat detail lengkap QRIS dalam modal
- Menampilkan QR code image
- Menampilkan QRIS string lengkap
- Informasi merchant, amount, fee, dll

#### Delete
- Menghapus record QRIS dari database
- Otomatis menghapus file QR code image
- Konfirmasi sebelum delete

### 4. Bulk Actions
- Bulk delete: Hapus multiple QRIS sekaligus
- Otomatis menghapus semua file QR code terkait

## Database Schema

```sql
CREATE TABLE qris_dynamics (
    id BIGINT PRIMARY KEY,
    qris_static_id BIGINT NULL,
    merchant_name VARCHAR(255),
    qris_string TEXT,
    amount DECIMAL(15,2),
    fee_type VARCHAR(255) DEFAULT 'Rupiah',
    fee_value DECIMAL(15,2) DEFAULT 0,
    qr_image_path VARCHAR(255) NULL,
    created_by BIGINT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (qris_static_id) REFERENCES qris_statics(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

## File Storage
QR code images disimpan di:
```
storage/app/public/qris-generated/
```

Format nama file:
```
qris-dynamic-{YmdHis}-{uniqid}.png
```

## Usage Example

### Generate QRIS
1. Buka halaman "QRIS Generator"
2. Pilih saved QRIS atau paste static QRIS
3. Masukkan amount
4. Atur fee (optional)
5. Klik "Generate Dynamic QRIS"
6. QRIS otomatis tersimpan ke history

### View History
1. Scroll ke bagian "Generated QRIS History"
2. Lihat semua QRIS yang pernah digenerate
3. Gunakan search untuk mencari merchant tertentu
4. Sort berdasarkan kolom yang diinginkan

### Download QR Code
1. Klik tombol "Download" pada row yang diinginkan
2. File PNG akan terdownload otomatis

### View Details
1. Klik tombol "View" pada row yang diinginkan
2. Modal akan menampilkan detail lengkap
3. QR code image ditampilkan dalam modal
4. QRIS string dapat dicopy

### Delete QRIS
1. Klik tombol "Delete" pada row yang diinginkan
2. Konfirmasi delete
3. Record dan file QR code akan terhapus

## Model Relationships

### QrisDynamic Model
```php
// Belongs to QrisStatic
$qrisDynamic->qrisStatic

// Belongs to User (creator)
$qrisDynamic->creator
```

## Permissions
Menggunakan Filament Shield untuk permission management:
- `view_qris::dynamic::generator`: Akses halaman
- `create_qris::dynamic::generator`: Generate QRIS
- `delete_qris::dynamic::generator`: Delete QRIS

## Notes
- QR code image otomatis dihapus saat record dihapus
- Bulk delete juga menghapus semua file terkait
- File disimpan di public storage (accessible via URL)
- Image size: 400x400 pixels dengan margin 10px
