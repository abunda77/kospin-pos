---
description: Alternative solution using separate counter table
---

# Alternative Solution: Separate Counter Table

If the database lock solution still has issues, you can use a separate counter table:

## Step 1: Create Migration for Counter Table

```bash
php artisan make:migration create_order_counters_table
```

## Step 2: Migration Content

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_counters', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->unsignedBigInteger('value')->default(0);
            $table->timestamps();
        });
        
        // Insert initial counter
        DB::table('order_counters')->insert([
            'key' => 'no_order',
            'value' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('order_counters');
    }
};
```

## Step 3: Update Order Model

Replace the no_order generation in `app/Models/Order.php`:

```php
// Generate sequential no_order using atomic counter
if (empty($model->no_order)) {
    $nextNumber = \DB::table('order_counters')
        ->where('key', 'no_order')
        ->lockForUpdate()
        ->increment('value');
    
    $model->no_order = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}
```

## Step 4: Run Migration

```bash
php artisan migrate
```

This solution uses MySQL's atomic `INCREMENT` operation which is guaranteed to be unique even under high concurrency.
