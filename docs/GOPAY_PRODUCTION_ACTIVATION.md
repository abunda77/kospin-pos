# GoPay Production Activation Guide

## 📋 Overview

Untuk menggunakan GoPay di Production environment, payment channel harus diaktifkan terlebih dahulu di Midtrans Dashboard. Proses ini memerlukan approval dari Midtrans team.

## ⚠️ Current Status

### ✅ Working
- **Sandbox Environment**: GoPay berfungsi dengan baik
- **Production VA**: Virtual Account berfungsi normal
- **Credentials**: Valid dan terverifikasi

### ❌ Not Working
- **Production GoPay**: Belum diaktifkan
- **Error Message**: 
  ```
  HTTP 402: Payment channel is not activated
  ```

## 🔧 Activation Steps

### Step 1: Login to Production Dashboard

1. Buka https://dashboard.midtrans.com/
2. Login dengan credentials Anda
3. **PENTING**: Pastikan toggle di kanan atas menunjukkan **"Production"**
   - Jika masih "Sandbox", klik toggle untuk switch ke Production

### Step 2: Navigate to Payment Configuration

1. Di sidebar kiri, klik **Settings**
2. Pilih **Payment Configuration** atau **Configuration**
3. Scroll ke section **E-Wallet** atau **GoPay**

### Step 3: Enable GoPay

1. Cari **GoPay** payment method
2. Klik tombol **Enable** atau toggle switch
3. Akan muncul form untuk diisi

### Step 4: Fill Business Information

Form yang perlu diisi (contoh):

#### Business Details
- **Business Name**: Nama bisnis Anda
- **Business Type**: Retail/E-commerce/Services/etc.
- **Business Address**: Alamat lengkap
- **Business Phone**: Nomor telepon bisnis
- **Business Email**: Email bisnis

#### Bank Account Information
- **Bank Name**: Nama bank
- **Account Number**: Nomor rekening
- **Account Holder Name**: Nama pemegang rekening
- **Branch**: Cabang bank (jika diperlukan)

#### Additional Documents (mungkin diperlukan)
- NPWP (Tax ID)
- NIB (Business License)
- Akta Pendirian (Company Deed)
- KTP Direktur (Director's ID)

### Step 5: Submit for Approval

1. Review semua informasi yang diisi
2. Centang agreement/terms & conditions
3. Klik **Submit** atau **Request Activation**
4. Akan muncul konfirmasi bahwa request sudah dikirim

### Step 6: Wait for Approval

1. Midtrans team akan review request Anda
2. Proses review: **1-3 hari kerja**
3. Anda akan menerima email notification:
   - **Approved**: GoPay sudah aktif
   - **Need More Info**: Perlu dokumen tambahan
   - **Rejected**: Dengan alasan penolakan

### Step 7: Verification

Setelah menerima email approval:

1. Login kembali ke Production Dashboard
2. Settings → Payment Configuration
3. Cek status GoPay: harus **"Active"** atau **"Enabled"**
4. Test transaksi dengan amount kecil (Rp 1.000)

## 📧 Email Notifications

Anda akan menerima email di berbagai tahap:

### 1. Submission Confirmation
```
Subject: GoPay Activation Request Received
Content: Request Anda sedang diproses
```

### 2. Additional Information Required
```
Subject: Additional Information Required for GoPay Activation
Content: Dokumen tambahan yang diperlukan
```

### 3. Approval Notification
```
Subject: GoPay Payment Channel Activated
Content: GoPay sudah aktif dan siap digunakan
```

## ⏰ Timeline

| Stage | Duration | Action Required |
|-------|----------|-----------------|
| Submit Request | Instant | Fill form & submit |
| Under Review | 1-3 hari kerja | Wait |
| Additional Info (if needed) | 1-2 hari | Provide documents |
| Final Approval | 1 hari | None |
| Testing | Instant | Test transaction |

**Total Estimated Time**: 2-5 hari kerja

## 🧪 Testing After Activation

### Test Checklist

1. **Clear Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Verify Configuration**
   ```bash
   php artisan tinker
   >>> config('services.midtrans.is_production')
   => true
   >>> config('services.midtrans.server_key')
   => "Mid-server-xxx"  // Production key
   ```

3. **Test Small Transaction**
   - Amount: Rp 1.000 - Rp 10.000
   - Generate QR Code
   - Scan dengan GoPay app
   - Complete payment
   - Verify order status updated

4. **Check Dashboard**
   - Login to Midtrans Dashboard
   - Transactions → All Transactions
   - Verify test transaction appears
   - Check status: Settlement

## 🚨 Common Issues During Activation

### Issue 1: Form Tidak Lengkap

**Solusi:**
- Pastikan semua field required terisi
- Upload dokumen yang diminta
- Gunakan format file yang benar (PDF, JPG, PNG)

### Issue 2: Dokumen Ditolak

**Alasan Umum:**
- Dokumen tidak jelas/blur
- Dokumen expired
- Nama tidak match dengan business name
- NPWP tidak valid

**Solusi:**
- Upload ulang dengan kualitas lebih baik
- Pastikan dokumen masih valid
- Pastikan nama konsisten di semua dokumen

### Issue 3: Business Type Tidak Sesuai

**Solusi:**
- Pilih business type yang paling sesuai
- Jika ragu, hubungi Midtrans support
- Siapkan penjelasan business model

### Issue 4: Approval Lama

**Jika lebih dari 5 hari kerja:**
- Cek email spam/junk folder
- Login ke dashboard untuk cek status
- Hubungi Midtrans support

## 📞 Contact Midtrans Support

### Email
- **General**: support@midtrans.com
- **Technical**: tech@midtrans.com
- **Business**: business@midtrans.com

### Phone
- **Indonesia**: +62 21 2922 0888
- **Hours**: Senin-Jumat, 09:00-18:00 WIB

### Live Chat
- Login ke Dashboard
- Klik icon chat di pojok kanan bawah
- Available: Senin-Jumat, 09:00-18:00 WIB

### Information to Provide
Saat menghubungi support, siapkan:
- **Merchant ID**: G387951376 (dari .env Anda)
- **Business Name**: Nama bisnis Anda
- **Issue**: "Request GoPay Production activation"
- **Timeline**: Kapan Anda submit request

## 🔄 Alternative: Use Sandbox Meanwhile

Sambil menunggu Production approval, gunakan Sandbox:

### Update .env
```env
# Sandbox Configuration
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx
MIDTRANS_IS_PRODUCTION=false
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Test
- GoPay akan berfungsi di Sandbox
- Gunakan Midtrans simulator untuk testing
- Tidak ada biaya transaksi

## 📊 Production vs Sandbox Comparison

| Feature | Sandbox | Production |
|---------|---------|------------|
| GoPay Status | ✅ Active | ⏳ Pending Activation |
| VA Status | ✅ Active | ✅ Active |
| Transaction | Test/Simulator | Real Money |
| Approval Required | ❌ No | ✅ Yes (1-3 days) |
| Cost | Free | Transaction fees apply |

## ✅ Post-Activation Checklist

Setelah GoPay Production aktif:

- [ ] Verify status "Active" di Dashboard
- [ ] Update .env ke Production credentials
- [ ] Clear all cache
- [ ] Test dengan small amount
- [ ] Verify webhook notifications working
- [ ] Test on different devices (mobile/desktop)
- [ ] Test QR Code scanning
- [ ] Test deeplink redirect (mobile)
- [ ] Monitor first few transactions
- [ ] Document any issues

## 📝 Notes

1. **Activation is per Merchant Account**
   - Setiap merchant account perlu aktivasi terpisah
   - Sandbox dan Production terpisah

2. **One-Time Process**
   - Setelah diaktifkan, GoPay akan tetap aktif
   - Tidak perlu aktivasi ulang

3. **Fees Apply**
   - Production transactions dikenakan biaya
   - Cek fee structure di Dashboard atau hubungi sales

4. **Compliance Required**
   - Pastikan bisnis comply dengan regulasi
   - Dokumen harus valid dan up-to-date

## 🎯 Summary

**Current Situation:**
- ✅ Sandbox GoPay: Working
- ✅ Production VA: Working
- ❌ Production GoPay: Not activated (HTTP 402)

**Action Required:**
1. Login to Production Dashboard
2. Enable GoPay payment method
3. Fill business information form
4. Submit for approval
5. Wait 1-3 hari kerja
6. Test after approval

**Meanwhile:**
- Use Sandbox for GoPay testing
- Use Production VA for real transactions
- Monitor email for approval notification

---

**Last Updated**: 2025-11-13  
**Status**: Waiting for Production GoPay activation
