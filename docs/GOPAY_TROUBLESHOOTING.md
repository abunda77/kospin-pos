# GoPay QR Code Troubleshooting Guide

## ✅ Masalah Terselesaikan

### Error: Route [checkout.index] not defined

**Gejala:**
- VA (Virtual Account) berfungsi normal
- GoPay QR Code tidak muncul
- Error di log: `Route [checkout.index] not defined`

**Penyebab:**
Route name yang salah di callback URL GoPay

**Solusi:**
Sudah diperbaiki dari:
```php
'callback_url' => route('checkout.index')  // ❌ Salah
```

Menjadi:
```php
'callback_url' => route('checkout')  // ✅ Benar
```

---

## 🔍 Cara Debugging GoPay QR Code

### 1. Cek Log Laravel
```bash
tail -f storage/logs/laravel.log
```

Cari error dengan keyword:
- `GoPay QR Generation Error`
- `Midtrans`
- `CoreApi`

### 2. Cek Browser Console
Buka Developer Tools (F12) → Console tab

Cari error:
- Network errors (failed requests)
- JavaScript errors
- Response dari `/checkout/generate-gopay-qr`

### 3. Cek Network Tab
Developer Tools → Network tab

Filter: `generate-gopay-qr`

Periksa:
- **Status Code**: Harus 200
- **Response**: Harus ada `success: true`
- **Response Body**: Harus ada `qr_code_url`

---

## 🐛 Common Issues & Solutions

### Issue 1: QR Code Tidak Muncul (Loading Terus)

**Kemungkinan Penyebab:**
1. JavaScript error
2. Route tidak ditemukan
3. Midtrans API error
4. Network timeout

**Cara Cek:**
```javascript
// Buka browser console dan jalankan:
fetch('/checkout/generate-gopay-qr', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        amount: 10000,
        order_id: 'TEST-' + Date.now()
    })
})
.then(r => r.json())
.then(data => console.log(data))
.catch(err => console.error(err));
```

**Solusi:**
- Pastikan route sudah terdaftar: `php artisan route:list | grep gopay`
- Clear cache: `php artisan route:clear`
- Restart server

---

### Issue 2: Error "Route not defined"

**Gejala:**
```
Route [checkout.index] not defined
```

**Solusi:**
✅ Sudah diperbaiki! Gunakan `route('checkout')` bukan `route('checkout.index')`

---

### Issue 3: Error "Payment channel is not activated" (HTTP 402)

**Gejala:**
```
Midtrans API is returning API error. HTTP status code: 402
API response: {"status_code":"402","status_message":"Payment channel is not activated."}
```

**Artinya:**
- ✅ Credentials BENAR (API bisa diakses)
- ❌ GoPay belum diaktifkan di Production Dashboard

**Solusi:**
1. Login ke [Midtrans Dashboard Production](https://dashboard.midtrans.com/)
2. Pastikan toggle menunjukkan **"Production"** (bukan Sandbox)
3. Settings → Payment Configuration
4. Aktifkan **GoPay/E-Wallet**
5. Isi form business information
6. Submit untuk approval
7. Tunggu email konfirmasi (1-3 hari kerja)

**Sementara Waktu:**
- Gunakan Sandbox untuk testing
- Atau gunakan payment method lain (VA, Credit Card)

---

### Issue 4: Error "QR Code URL not found in response"

**Kemungkinan Penyebab:**
1. Response format dari Midtrans berubah
2. Midtrans API error
3. Network timeout

**Cara Cek:**
```php
// Tambahkan di CheckoutController.php setelah $response = CoreApi::charge($params);
Log::info('Full Midtrans Response:', ['response' => json_encode($response)]);
```

**Solusi:**
Cek log untuk melihat response sebenarnya dari Midtrans

---

### Issue 5: Credentials Invalid

**Gejala:**
- Error 401 Unauthorized
- Error "Access denied"

**Cara Cek:**
```bash
# Cek apakah credentials sudah benar
php artisan tinker
>>> config('services.midtrans.server_key')
>>> config('services.midtrans.is_production')
```

**Solusi:**
1. Pastikan `.env` sudah benar:
   ```env
   MIDTRANS_SERVER_KEY=Mid-server-xxx  # Production
   # atau
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxx  # Sandbox
   ```
2. Clear config cache:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

---

### Issue 6: CORS Error

**Gejala:**
```
Access to fetch at '...' from origin '...' has been blocked by CORS policy
```

**Solusi:**
Ini bukan masalah CORS karena request ke backend sendiri, bukan ke Midtrans.
Cek apakah:
- CSRF token valid
- Route accessible
- Middleware tidak blocking

---

## 🧪 Testing Checklist

### Pre-Testing
- [ ] Midtrans credentials sudah di-set di `.env`
- [ ] Config cache sudah di-clear
- [ ] Route sudah terdaftar
- [ ] GoPay sudah diaktifkan di Dashboard

### Testing Steps
1. [ ] Buka halaman checkout
2. [ ] Pilih metode pembayaran "Midtrans"
3. [ ] Pilih "GoPay / E-Wallet"
4. [ ] QR Code muncul (tidak loading terus)
5. [ ] QR Code bisa di-scan dengan GoPay/QRIS app
6. [ ] Setelah bayar, status order berubah

### Post-Testing
- [ ] Cek log tidak ada error
- [ ] Cek database order status
- [ ] Cek Midtrans Dashboard untuk transaksi

---

## 📊 Expected Response Format

### Success Response
```json
{
    "success": true,
    "transaction_id": "231c79c5-e39e-4993-86da-cadcaee56c1d",
    "qr_code_url": "https://api.midtrans.com/v2/gopay/.../qr-code",
    "deeplink_url": "https://gojek.link/gopay/...",
    "amount_formatted": "44.000",
    "status": "pending"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Failed to generate GoPay QR: [error detail]"
}
```

---

## 🔗 Useful Commands

```bash
# Clear all cache
php artisan optimize:clear

# Check routes
php artisan route:list | grep gopay

# Check config
php artisan tinker
>>> config('services.midtrans')

# Watch logs
tail -f storage/logs/laravel.log

# Test API manually
curl -X POST http://localhost:8003/checkout/generate-gopay-qr \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{"amount": 10000, "order_id": "TEST-123"}'
```

---

## 📞 Support

Jika masalah masih berlanjut:

1. **Cek Midtrans Status**: https://status.midtrans.com/
2. **Midtrans Support**: support@midtrans.com
3. **Midtrans Documentation**: https://docs.midtrans.com/
4. **Check Laravel Log**: `storage/logs/laravel.log`

---

## ✅ Verification

Setelah fix, pastikan:
- ✅ QR Code muncul dengan benar
- ✅ Tidak ada error di log
- ✅ Bisa di-scan dengan GoPay/QRIS app
- ✅ Deeplink button muncul (untuk mobile)
- ✅ Callback URL benar
