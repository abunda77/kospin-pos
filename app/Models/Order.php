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

            // Generate unique no_order with random prefix
            if (empty($model->no_order)) {
                do {
                    // Generate random 3-letter prefix (A-Z)
                    $prefix = '';
                    for ($i = 0; $i < 3; $i++) {
                        $prefix .= chr(rand(65, 90)); // A-Z
                    }
                    
                    // Use timestamp-based number (last 5 digits of microtime)
                    $number = substr((string) (microtime(true) * 10000), -5);
                    
                    // Format: ABC-12345 (e.g., UHJ-45678)
                    $candidate = $prefix . '-' . $number;
                } while (static::where('no_order', $candidate)->exists());
                
                $model->no_order = $candidate;
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

