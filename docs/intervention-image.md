# Intervention Image

Intervention Image adalah library PHP open source untuk manipulasi dan pemrosesan gambar yang mudah dan ekspresif. Library ini mendukung berbagai driver seperti GD, Imagick, dan libvips, serta dapat digunakan untuk membuat thumbnail, watermark, resize, crop, dan berbagai efek gambar lainnya.

## Fitur Utama

-   API seragam untuk GD, Imagick, dan libvips
-   Mendukung manipulasi gambar animasi (GIF)
-   Mendukung berbagai format gambar (JPG, PNG, GIF, WebP, dsb)
-   Mendukung manipulasi warna, transparansi, dan profil warna
-   Mendukung penambahan teks, watermark, dan efek gambar
-   PSR-12 compliant dan mudah diintegrasikan dengan framework PHP

## Instalasi

Jalankan perintah berikut di root project Anda:

```bash
composer require intervention/image
```

## Penggunaan Dasar

### Membaca dan Membuat Gambar

```php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$image = $manager->read('images/example.jpg');
```

### Resize Gambar

```php
$image->scale(width: 300); // Resize proporsional ke lebar 300px
```

### Menambahkan Watermark

```php
$image->place('images/watermark.png');
```

### Simpan Gambar ke Format Lain

```php
$image->toPng()->save('images/foo.png');
```

## Contoh Lain

### Crop, Rotate, dan Efek

```php
$image->crop(width: 100, height: 100);
$image->rotate(45);
$image->blur(5);
```

### Menambahkan Teks

```php
$image->text('Hello World', x: 50, y: 50);
```

## Dokumentasi Lengkap

-   [Intervention Image Documentation](https://image.intervention.io/)
-   [GitHub Intervention Image](https://github.com/Intervention/image)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Intervention Image._
