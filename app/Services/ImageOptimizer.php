<?php

namespace App\Services;

use Intervention\Image\ImageManager;

class ImageOptimizer
{
    public static function optimize($path, $quality = 80)
    {
        $image = \Intervention\Image\ImageManager::gd()->read($path);

        // Resize jika gambar terlalu besar
        if ($image->width() > 1024 || $image->height() > 1024) {
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Compress dan simpan
        $image->save($path, $quality);

        return $path;
    }
}
