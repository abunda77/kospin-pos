<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $fillable = [
        'filename',
        'path',
        'size',
        'type',
        'status',
        'notes'
    ];

    public const TYPE_MANUAL = 'manual';
    public const TYPE_SCHEDULED = 'scheduled';

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
