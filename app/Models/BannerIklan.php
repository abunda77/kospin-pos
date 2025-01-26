<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerIklan extends Model
{
    protected $table = 'banner_iklan';

    protected $fillable = [
        'judul_iklan',
        'banner_image',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'pemilik_iklan',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];
}
