# Integrasi QRIS Static dengan Payment Flow

## Overview

Resource QRIS Static memungkinkan Anda untuk menyimpan dan mengelola kode QRIS statis yang dapat digunakan untuk pembayaran di sistem POS.

## Arsitektur

```
┌─────────────────┐
│  Filament Admin │
│  QRIS Resource  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  QrisStatic     │
│  Model          │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  QrisHelper     │
│  (Extract QR)   │
└─────────────────┘
```

## Use Cases

### 1. Pembayaran di POS

Ketika customer memilih pembayaran QRIS:

```php
// Di Controller atau Livewire POS
use App\Models\QrisStatic;

public function showQrisPayment()
{
    $qris = QrisStatic::active()->first();
    
    if (!$qris) {
        return $this->notify('error', 'QRIS tidak tersedia');
    }
    
    return view('pos.qris-payment', [
        'qris_string' => $qris->qris_string,
        'qris_image' => $qris->qris_image_url,
        'merchant_name' => $qris->merchant_name,
    ]);
}
```

### 2. Generate QR Code untuk Struk

```php
use App\Models\QrisStatic;
use Picqer\Barcode\BarcodeGeneratorPNG;

public function generateQrisForReceipt($orderId)
{
    $qris = QrisStatic::active()->first();
    
    if ($qris) {
        $generator = new BarcodeGeneratorPNG();
        $qrCode = $generator->getBarcode(
            $qris->qris_string, 
            $generator::TYPE_CODE_128
        );
        
        return base64_encode($qrCode);
    }
    
    return null;
}
```

### 3. API Endpoint untuk Mobile App

```php
// routes/api.php
Route::get('/payment/qris', function () {
    $qris = QrisStatic::active()->first();
    
    if (!$qris) {
        return response()->json([
            'success' => false,
            'message' => 'QRIS tidak tersedia'
        ], 404);
    }
    
    return response()->json([
        'success' => true,
        'data' => [
            'qris_string' => $qris->qris_string,
            'merchant_name' => $qris->merchant_name,
            'qr_image_url' => $qris->qris_image_url,
        ]
    ]);
});
```

### 4. Integrasi dengan Order

```php
use App\Models\Order;
use App\Models\QrisStatic;

public function createOrderWithQris($orderData)
{
    $order = Order::create($orderData);
    
    // Attach QRIS info to order
    $qris = QrisStatic::active()->first();
    
    if ($qris) {
        $order->update([
            'payment_qris_id' => $qris->id,
            'payment_qris_merchant' => $qris->merchant_name,
        ]);
    }
    
    return $order;
}
```

## Workflow Pembayaran QRIS

### Static QRIS Flow

```
1. Customer memilih pembayaran QRIS
   ↓
2. Sistem menampilkan QR code dari QrisStatic
   ↓
3. Customer scan dengan aplikasi e-wallet
   ↓
4. Customer input nominal pembayaran
   ↓
5. Customer konfirmasi pembayaran
   ↓
6. Kasir verifikasi pembayaran masuk
   ↓
7. Order selesai
```

### Dynamic QRIS Flow (Future Enhancement)

```
1. Customer memilih pembayaran QRIS
   ↓
2. Sistem generate dynamic QRIS dengan nominal
   ↓
3. Customer scan QR code
   ↓
4. Nominal sudah terisi otomatis
   ↓
5. Customer konfirmasi
   ↓
6. Sistem terima callback otomatis
   ↓
7. Order selesai otomatis
```

## Best Practices

### 1. Multiple QRIS Management

Jika Anda memiliki beberapa QRIS:

```php
// Pilih QRIS berdasarkan lokasi toko
$qris = QrisStatic::active()
    ->where('name', 'like', "%{$storeName}%")
    ->first();

// Atau round-robin untuk load balancing
$qrisCount = QrisStatic::active()->count();
$index = $orderId % $qrisCount;
$qris = QrisStatic::active()->skip($index)->first();
```

### 2. Fallback Strategy

```php
public function getQrisWithFallback()
{
    // Try to get primary QRIS
    $qris = QrisStatic::active()
        ->where('name', 'Primary')
        ->first();
    
    // Fallback to any active QRIS
    if (!$qris) {
        $qris = QrisStatic::active()->first();
    }
    
    // Fallback to inactive if no active
    if (!$qris) {
        $qris = QrisStatic::orderBy('updated_at', 'desc')->first();
    }
    
    return $qris;
}
```

### 3. Caching untuk Performance

```php
use Illuminate\Support\Facades\Cache;

public function getCachedQris()
{
    return Cache::remember('active_qris', 3600, function () {
        return QrisStatic::active()->first();
    });
}

// Clear cache ketika QRIS diupdate
// Di QrisStaticResource atau Observer
protected function afterSave(): void
{
    Cache::forget('active_qris');
}
```

### 4. Logging Penggunaan QRIS

```php
use Illuminate\Support\Facades\Log;

public function logQrisUsage($qrisId, $orderId)
{
    Log::info('QRIS Payment', [
        'qris_id' => $qrisId,
        'order_id' => $orderId,
        'timestamp' => now(),
    ]);
}
```

## Migration Path: Static → Dynamic

Jika di masa depan ingin upgrade ke Dynamic QRIS:

1. Tetap gunakan QrisStatic untuk fallback
2. Tambah service untuk generate dynamic QRIS
3. Prioritaskan dynamic, fallback ke static jika gagal

```php
public function getQrisForPayment($amount)
{
    // Try dynamic first
    try {
        $dynamicQris = $this->generateDynamicQris($amount);
        return $dynamicQris;
    } catch (\Exception $e) {
        // Fallback to static
        Log::warning('Dynamic QRIS failed, using static', [
            'error' => $e->getMessage()
        ]);
        
        return QrisStatic::active()->first();
    }
}
```

## Security Considerations

1. **Validasi QRIS String**: Selalu validasi dengan `QrisHelper::isValidQris()`
2. **Access Control**: Gunakan Filament Shield untuk membatasi akses
3. **Audit Log**: Log semua perubahan QRIS
4. **Backup**: Backup QRIS string secara berkala

## Testing

```php
// tests/Feature/QrisStaticTest.php
public function test_can_get_active_qris()
{
    $qris = QrisStatic::factory()->create([
        'is_active' => true,
    ]);
    
    $activeQris = QrisStatic::active()->first();
    
    $this->assertEquals($qris->id, $activeQris->id);
}

public function test_qris_helper_validates_correctly()
{
    $validQris = '00020101021126...5802ID...';
    $invalidQris = 'invalid';
    
    $this->assertTrue(QrisHelper::isValidQris($validQris));
    $this->assertFalse(QrisHelper::isValidQris($invalidQris));
}
```

## Monitoring

Dashboard metrics yang berguna:

- Total QRIS aktif
- QRIS yang paling sering digunakan
- Waktu rata-rata pembayaran QRIS
- Success rate pembayaran QRIS

```php
// Di Dashboard Widget
public function getQrisMetrics()
{
    return [
        'total_qris' => QrisStatic::count(),
        'active_qris' => QrisStatic::active()->count(),
        'last_updated' => QrisStatic::latest('updated_at')->first()?->updated_at,
    ];
}
```

## Troubleshooting

### QRIS tidak muncul di POS
- Cek apakah ada QRIS yang aktif: `QrisStatic::active()->count()`
- Cek permission user untuk akses QRIS
- Clear cache: `Cache::forget('active_qris')`

### QR Code tidak bisa di-scan
- Validasi string QRIS dengan `QrisHelper::isValidQris()`
- Cek kualitas gambar QR code
- Regenerate QR code dari string

### Merchant name tidak terdeteksi
- Cek format string QRIS
- Manual input merchant name jika auto-detect gagal
