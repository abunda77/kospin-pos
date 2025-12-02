# Fix: Duplicate Order Number (no_order) Race Condition

## 📋 Ringkasan Masalah

**Error yang Terjadi:**
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '000001' for key 'orders_no_order_unique'
```

**Penyebab:**
Race condition terjadi ketika dua atau lebih request mencoba membuat order secara bersamaan. Kode lama menggunakan logika sequential number generation di dalam model event `creating()` tanpa database locking, sehingga dua request bisa membaca `no_order` terakhir yang sama dan mencoba insert dengan nomor yang sama.

## ✅ Solusi yang Diterapkan

### 1. **Model Order.php**
- **Menghapus** logika generate `no_order` dari event `creating()`
- **Menambahkan** static method `generateNextOrderNumber()` dengan database locking

```php
/**
 * Generate next sequential order number with database locking
 * This method MUST be called within a database transaction
 * 
 * @return string
 */
public static function generateNextOrderNumber(): string
{
    // Lock the last order to prevent race conditions
    $lastOrder = static::orderBy('no_order', 'desc')
        ->lockForUpdate()
        ->first();
    
    $nextNumber = $lastOrder ? intval($lastOrder->no_order) + 1 : 1;
    
    return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}
```

**Mengapa di-remove dari event `creating()`?**
- Event `creating()` dipanggil SEBELUM transaction dimulai
- `lockForUpdate()` tidak efektif di luar transaction
- Lebih baik generate no_order secara eksplisit dalam transaction

### 2. **Controllers yang Diupdate**

Semua controller yang membuat order telah diupdate untuk:
1. Membungkus order creation dalam `DB::transaction()`
2. Generate `no_order` menggunakan `Order::generateNextOrderNumber()` SEBELUM create
3. Menyimpan `no_order` ke dalam order data

**File yang diupdate:**
- ✅ `app/Http/Controllers/CheckoutController.php` (3 methods: `process()`, `store()`, `processPayment()`)
- ✅ `app/Livewire/Pos.php` (method: `checkout()`)
- ✅ `app/Http/Controllers/Api/OrderController.php` (method: `store()`)
- ✅ `app/Http/Controllers/Api/CheckoutUserController.php` (method: `process()`)

### 3. **Cara Kerja Solusi**

**Timeline SEBELUM fix (Race Condition):**
```
Request A: Baca lastOrder → dapat "000001"
Request B: Baca lastOrder → dapat "000001" (A belum commit!)
Request A: Insert no_order = "000002" ✅
Request B: Insert no_order = "000002" ❌ DUPLICATE!
```

**Timeline SETELAH fix (Thread-Safe):**
```
Request A: START TRANSACTION
Request A: lockForUpdate() → Baca lastOrder = "000001" 🔒
Request B: START TRANSACTION
Request B: lockForUpdate() → MENUNGGU... ⏳ (karena A masih lock)
Request A: Insert no_order = "000002" ✅
Request A: COMMIT → Lock dilepas 🔓
Request B: Baca lastOrder = "000002" 🔒
Request B: Insert no_order = "000003" ✅
Request B: COMMIT 🔓
```

## 🔧 Contoh Penggunaan

```php
// BENAR ✅
$order = \DB::transaction(function () use ($orderData) {
    // Generate no_order dengan locking
    $orderData['no_order'] = Order::generateNextOrderNumber();
    
    // Create order
    $order = Order::create($orderData);
    
    // ... simpan order products, dll
    
    return $order;
});

// SALAH ❌ (tanpa transaction)
$orderData['no_order'] = Order::generateNextOrderNumber(); // Tidak aman!
$order = Order::create($orderData);
```

## ⚠️ Catatan Penting

1. **Database Engine**: Pastikan menggunakan **InnoDB** (bukan MyISAM)
   ```sql
   SHOW TABLE STATUS WHERE Name = 'orders';
   -- Engine harus InnoDB
   ```

2. **Transaction Isolation Level**: Laravel default menggunakan `REPEATABLE READ` yang sudah sesuai

3. **Performance Impact**: 
   - Locking akan sedikit memperlambat concurrent requests
   - Trade-off yang diperlukan untuk data integrity
   - Untuk high-traffic, pertimbangkan menggunakan Redis atau database sequence

4. **Testing**: Pastikan test dengan concurrent requests untuk memverifikasi fix

## 🧪 Testing

### Manual Test
```bash
# Test dengan concurrent requests (Linux/Mac)
for i in {1..10}; do
  curl -X POST http://localhost/checkout/process \
    -H "Content-Type: application/json" \
    -d '{"payment_method_id":1,"name":"Test","whatsapp":"123","address":"Test"}' &
done
wait

# Cek apakah ada duplicate
php artisan tinker
>>> Order::select('no_order')->groupBy('no_order')->havingRaw('COUNT(*) > 1')->get()
// Harus kosong []
```

### Load Test dengan Apache Bench
```bash
# 100 requests, 10 concurrent
ab -n 100 -c 10 -p post_data.json -T application/json http://localhost/checkout/process
```

## 📊 Perbandingan

| Aspek | Sebelum | Sesudah |
|-------|---------|---------|
| **Race Condition** | ❌ Rentan | ✅ Aman |
| **Duplicate Entry** | ❌ Bisa terjadi | ✅ Tidak mungkin |
| **Atomicity** | ❌ Tidak ada | ✅ Semua atau tidak sama sekali |
| **Database Locking** | ❌ Tidak ada | ✅ Pessimistic locking |
| **Performance** | ⚡ Cepat tapi tidak aman | ⚡ Sedikit lebih lambat tapi aman |

## 🔍 Troubleshooting

### Jika masih terjadi duplicate:
1. Pastikan semua tempat yang create Order sudah menggunakan transaction
2. Cek database engine: `SHOW TABLE STATUS WHERE Name = 'orders'`
3. Pastikan unique constraint ada: `SHOW INDEX FROM orders WHERE Key_name = 'orders_no_order_unique'`
4. Clear cache: `php artisan cache:clear && php artisan config:clear`

### Jika performance menurun:
1. Monitor slow query log
2. Pertimbangkan indexing tambahan
3. Untuk high-traffic, gunakan Redis atomic increment atau database sequence

## 📝 Changelog

**2025-12-02**
- ✅ Removed no_order generation from Order model boot event
- ✅ Added `Order::generateNextOrderNumber()` static method with locking
- ✅ Updated CheckoutController (3 methods)
- ✅ Updated Pos Livewire component
- ✅ Updated API OrderController
- ✅ Updated API CheckoutUserController
- ✅ All order creation now wrapped in DB::transaction()

## 👥 Kontributor
- Fixed by: Antigravity AI
- Date: 2025-12-02
- Issue: Duplicate entry for key 'orders_no_order_unique'
