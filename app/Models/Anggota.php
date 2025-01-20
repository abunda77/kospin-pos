<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $fillable = [
        'nama_lengkap',
        'nik',
        'total_pembelian'
    ];

    protected $casts = [
        'total_pembelian' => 'decimal:2'
    ];
}
