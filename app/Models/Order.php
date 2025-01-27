<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Generate UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            // Generate sequential no_order
            if (empty($model->no_order)) {
                $lastOrder = static::orderBy('no_order', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->no_order) + 1 : 1;
                $model->no_order = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'id',
        'no_order',
        'name',
        'email',
        'phone',
        'birthday',
        'total_price',
        'note',
        'payment_method_id',
        'anggota_id',
        'discount',
        'whatsapp',
        'address',
        'status'
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function products(): HasMany
    {
        return $this->orderProducts()->with('product');
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}
