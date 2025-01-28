<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VoucherDiskon extends Model
{
    protected $fillable = [
        'kode_voucher',
        'nilai_discount',
        'jenis_discount',
        'expired_time',
        'stok_voucher'
    ];

    protected $casts = [
        'expired_time' => 'datetime',
        'nilai_discount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_voucher)) {
                $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                $suffix = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $model->kode_voucher = $prefix . '-' . $suffix;
            }
        });
    }

    public function isValid()
    {
        return $this->stok_voucher > 0 && $this->expired_time > Carbon::now();
    }

    public function calculateDiscount($totalAmount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->jenis_discount === 'prosentase') {
            return ($this->nilai_discount / 100) * $totalAmount;
        }

        return min($this->nilai_discount, $totalAmount);
    }

    public function useVoucher()
    {
        if ($this->stok_voucher > 0) {
            $this->decrement('stok_voucher');
            return true;
        }
        return false;
    }
}
