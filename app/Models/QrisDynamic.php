<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrisDynamic extends Model
{
    protected $fillable = [
        'qris_static_id',
        'merchant_name',
        'qris_string',
        'amount',
        'fee_type',
        'fee_value',
        'qr_image_path',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_value' => 'decimal:2',
    ];

    public function qrisStatic(): BelongsTo
    {
        return $this->belongsTo(QrisStatic::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
