# Visualisasi: Race Condition vs Thread-Safe Solution

## 🔴 SEBELUM FIX - Race Condition (Bermasalah)

```
┌─────────────────────────────────────────────────────────────────┐
│                    Timeline Race Condition                       │
└─────────────────────────────────────────────────────────────────┘

Request A                          Request B
    │                                  │
    ├─ Read lastOrder                  │
    │  (no_order = "000001")           │
    │                                  ├─ Read lastOrder
    │                                  │  (no_order = "000001") ⚠️ SAMA!
    │                                  │
    ├─ Calculate next: 000002          │
    │                                  ├─ Calculate next: 000002 ⚠️ SAMA!
    │                                  │
    ├─ INSERT no_order = "000002" ✅   │
    │                                  │
    │                                  ├─ INSERT no_order = "000002" ❌
    │                                  │  ERROR: Duplicate entry!
    ▼                                  ▼

HASIL: Order A berhasil, Order B GAGAL dengan error duplicate!
```

## ✅ SESUDAH FIX - Thread-Safe dengan Locking

```
┌─────────────────────────────────────────────────────────────────┐
│              Timeline dengan Database Locking                    │
└─────────────────────────────────────────────────────────────────┘

Request A                          Request B
    │                                  │
    ├─ START TRANSACTION               │
    │                                  │
    ├─ lockForUpdate()                 │
    │  Read lastOrder 🔒                │
    │  (no_order = "000001")           │
    │  [LOCK ACQUIRED]                 │
    │                                  ├─ START TRANSACTION
    │                                  │
    │                                  ├─ lockForUpdate()
    │                                  │  [WAITING...] ⏳
    │                                  │  (Menunggu lock dilepas)
    ├─ Calculate next: 000002          │
    │                                  │
    ├─ INSERT no_order = "000002" ✅   │
    │                                  │
    ├─ COMMIT                          │
    │  [LOCK RELEASED] 🔓              │
    │                                  │
    │                                  ├─ Read lastOrder 🔒
    │                                  │  (no_order = "000002")
    │                                  │  [LOCK ACQUIRED]
    │                                  │
    │                                  ├─ Calculate next: 000003
    │                                  │
    │                                  ├─ INSERT no_order = "000003" ✅
    │                                  │
    │                                  ├─ COMMIT
    │                                  │  [LOCK RELEASED] 🔓
    ▼                                  ▼

HASIL: Order A = "000002" ✅, Order B = "000003" ✅
       Keduanya berhasil, tidak ada duplicate!
```

## 🔧 Kode Implementasi

### ❌ SEBELUM (Tidak Aman)
```php
// Di Model Order boot event
static::creating(function ($model) {
    if (empty($model->no_order)) {
        // ⚠️ Tidak ada locking!
        $lastOrder = static::orderBy('no_order', 'desc')->first();
        $nextNumber = $lastOrder ? intval($lastOrder->no_order) + 1 : 1;
        $model->no_order = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
});

// Di Controller
$order = Order::create($orderData); // ❌ Rentan race condition
```

### ✅ SESUDAH (Aman)
```php
// Di Model Order
public static function generateNextOrderNumber(): string
{
    // ✅ Dengan locking!
    $lastOrder = static::orderBy('no_order', 'desc')
        ->lockForUpdate()  // 🔒 LOCK
        ->first();
    
    $nextNumber = $lastOrder ? intval($lastOrder->no_order) + 1 : 1;
    return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}

// Di Controller
$order = \DB::transaction(function () use ($orderData) {
    // ✅ Generate dalam transaction
    $orderData['no_order'] = Order::generateNextOrderNumber();
    return Order::create($orderData);
});
```

## 📊 Perbandingan

| Aspek | Sebelum | Sesudah |
|-------|---------|---------|
| **Locking** | ❌ Tidak ada | ✅ lockForUpdate() |
| **Transaction** | ❌ Tidak wajib | ✅ Wajib |
| **Race Condition** | ❌ Rentan | ✅ Aman |
| **Concurrent Safety** | ❌ Tidak aman | ✅ Thread-safe |
| **Duplicate Risk** | ❌ Tinggi | ✅ Tidak mungkin |
| **Performance** | ⚡ Cepat | ⚡ Sedikit lambat |

## 🎯 Kesimpulan

**Sebelum Fix:**
- Request concurrent bisa baca nilai yang sama
- Menyebabkan duplicate entry error
- Data integrity tidak terjaga

**Sesudah Fix:**
- Request concurrent harus antri (queued)
- Setiap request dapat nomor unik
- Data integrity terjaga 100%

---

**Trade-off**: Sedikit penurunan performance untuk concurrent requests, tapi ini **WAJIB** untuk menjaga data integrity!
