# GoPay QR Code Integration

## Overview
Implementasi pembayaran GoPay/E-Wallet menggunakan Midtrans Core API dengan menampilkan QR Code yang dapat di-scan oleh aplikasi GoPay, Gojek, atau aplikasi QRIS lainnya.

## Fitur
- Generate QR Code GoPay secara dinamis via Midtrans API
- Menampilkan QR Code di halaman checkout
- Support deeplink untuk redirect ke aplikasi GoPay (mobile)
- Compatible dengan QRIS standard

## Flow Pembayaran

### Desktop/Web Browser
1. Customer memilih metode pembayaran Midtrans
2. Customer memilih "GoPay / E-Wallet" dari payment type selector
3. System generate QR Code via Midtrans API
4. QR Code ditampilkan di halaman checkout
5. Customer scan QR Code menggunakan aplikasi GoPay/Gojek/QRIS
6. Customer konfirmasi pembayaran di aplikasi
7. Webhook notification diterima untuk update status order

### Mobile
1. Customer memilih metode pembayaran Midtrans
2. Customer memilih "GoPay / E-Wallet"
3. System generate QR Code dan deeplink
4. Customer dapat:
   - Scan QR Code, atau
   - Klik tombol "Buka GoPay App" untuk redirect langsung

## Technical Implementation

### Frontend (checkout.blade.php)
- Form GoPay dengan QR Code display
- JavaScript function `generateGopayQr()` untuk request ke backend
- Loading state dan error handling
- Retry mechanism jika generation gagal

### Backend (CheckoutController.php)
- Method `generateGopayQr()` untuk create transaction via Midtrans
- Extract QR Code URL dan deeplink dari response
- Return JSON response dengan QR image URL

### Route
```php
Route::post('/checkout/generate-gopay-qr', [CheckoutController::class, 'generateGopayQr'])
    ->name('checkout.generate-gopay-qr');
```

## Midtrans API Request
```php
$params = [
    'payment_type' => 'gopay',
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => (int) $amount,
    ],
    'gopay' => [
        'enable_callback' => true,
        'callback_url' => route('checkout.index')
    ]
];

$response = CoreApi::charge($params);
```

## Response Structure
```json
{
    "success": true,
    "transaction_id": "231c79c5-e39e-4993-86da-cadcaee56c1d",
    "qr_code_url": "https://api.sandbox.veritrans.co.id/v2/gopay/.../qr-code",
    "deeplink_url": "https://simulator.sandbox.midtrans.com/gopay/ui/checkout?...",
    "amount_formatted": "44.000",
    "status": "pending"
}
```

## Testing

### Sandbox Environment
1. Pilih metode pembayaran Midtrans
2. Pilih "GoPay / E-Wallet"
3. QR Code akan muncul
4. Untuk testing, gunakan Midtrans simulator atau test credentials

### Production
- Pastikan Midtrans credentials sudah di-set ke production
- Test dengan aplikasi GoPay/Gojek real
- Monitor webhook notifications

## Troubleshooting

### QR Code tidak muncul
- Check Midtrans credentials di `.env`
- Check log di `storage/logs/laravel.log`
- Pastikan `SERVER_KEY` valid
- Verify network connectivity ke Midtrans API

### Error "QR Code URL not found"
- Response dari Midtrans tidak sesuai format
- Check Midtrans account configuration
- Verify GoPay payment method is enabled di Midtrans dashboard

## Configuration
File: `config/services.php`
```php
'midtrans' => [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
],
```

File: `.env`
```
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

## References
- [Midtrans GoPay Documentation](https://docs.midtrans.com/docs/coreapi-e-money-integration)
- [QRIS Standard](https://www.bi.go.id/id/edukasi/Pages/QR-Code-Indonesian-Standard.aspx)
