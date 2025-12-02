<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

            // Generate sequential no_order with database lock to prevent race condition
            if (empty($model->no_order)) {
                \DB::transaction(function () use ($model) {
                    // Lock the last order row to prevent concurrent reads
                    $lastOrder = static::orderBy('no_order', 'desc')
                        ->lockForUpdate()
                        ->first();
                    
                    $nextNumber = $lastOrder ? intval($lastOrder->no_order) + 1 : 1;
                    $model->no_order = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                });
            }

            // Set current user as cashier if not set
            if (empty($model->user_id) && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    protected $fillable = [
        'payment_method_id',
        'anggota_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'address',
        'birthday',
        'no_order',
        'total_price',
        'discount',
        'note',
        'status',
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'voucher_id',
        'transaction_id',
        'payment_url',
        'payment_details',
        'qris_dynamic_id',
    ];

    protected $casts = [
        'payment_details' => 'json',
        'birthday' => 'date',
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

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(VoucherDiskon::class, 'voucher_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function qrisDynamic(): BelongsTo
    {
        return $this->belongsTo(QrisDynamic::class, 'qris_dynamic_id');
    }
}

