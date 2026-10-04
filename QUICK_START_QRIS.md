# Quick Start - QRIS Static Resource

## 🚀 3 Langkah Cepat

### 1️⃣ Install Library
```bash
composer require khanamiryan/qrcode-detector-decoder
```

### 2️⃣ Run Migration
```bash
php artisan migrate
```

### 3️⃣ Akses Resource
Buka: **Filament Admin** → **Menejemen keuangan** → **QRIS Statis**

---

## 📝 Cara Menambah QRIS

### Opsi A: Upload Gambar
1. Klik **Buat**
2. Isi **Nama** (contoh: "QRIS Toko Utama")
3. **Upload Gambar QRIS** (PNG/JPG)
4. String QRIS dan Merchant akan terisi otomatis ✨
5. Klik **Simpan**

### Opsi B: Paste String
1. Klik **Buat**
2. Isi **Nama**
3. **Paste String QRIS** di textarea
4. Merchant akan terisi otomatis ✨
5. Klik **Simpan**

---

## 💻 Cara Menggunakan di Kode

### Ambil QRIS Aktif
```php
use App\Models\QrisStatic;

$qris = QrisStatic::active()->first();

echo $qris->name;           // "QRIS Toko Utama"
echo $qris->merchant_name;  // "Toko ABC"
echo $qris->qris_string;    // "00020101021126..."
echo $qris->qris_image_url; // "https://..."
```

### Untuk Pembayaran
```php
$qris = QrisStatic::active()->first();

if ($qris) {
    // Tampilkan QR code ke customer
    return view('payment.qris', [
        'qris_string' => $qris->qris_string,
        'qris_image' => $qris->qris_image_url,
    ]);
}
```

---

## 🎯 Fitur Lengkap

✅ Upload gambar QR → Auto-extract string  
✅ Paste string → Auto-detect merchant  
✅ View modal dengan QR code  
✅ Copy string dengan 1 klik  
✅ Filter aktif/tidak aktif  
✅ Search nama & merchant  

---

## 📚 Dokumentasi Lengkap

- **QRIS_RESOURCE_SUMMARY.md** - Overview lengkap
- **QRIS_SETUP.md** - Setup detail
- **docs/QRIS_INTEGRATION.md** - Integrasi payment
- **app/Filament/Resources/QrisStaticResource/README.md** - Dokumentasi resource
- **app/Filament/Resources/QrisStaticResource/USAGE_EXAMPLE.php.txt** - Contoh kode

---

## ❓ Troubleshooting

**Library error?**
```bash
composer require khanamiryan/qrcode-detector-decoder
```

**Gambar tidak muncul?**
```bash
php artisan storage:link
```

**Cache issue?**
```bash
php artisan cache:clear
```

---

## ✨ That's it!

Anda sudah siap menggunakan QRIS Static Resource! 🎉
