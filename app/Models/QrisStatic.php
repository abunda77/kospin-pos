<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrisStatic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'qris_string',
        'qris_image',
        'merchant_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk mendapatkan QRIS yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full URL for the QRIS image
     */
    public function getQrisImageUrlAttribute(): ?string
    {
        if ($this->qris_image) {
            return asset('storage/'.$this->qris_image);
        }

        return null;
    }
}
