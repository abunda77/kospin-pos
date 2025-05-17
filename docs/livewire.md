# Laravel Livewire

Laravel Livewire adalah framework full-stack untuk Laravel yang memungkinkan Anda membangun antarmuka web dinamis dan interaktif tanpa harus meninggalkan kenyamanan PHP dan Blade. Livewire memudahkan pembuatan komponen interaktif tanpa perlu menulis JavaScript secara langsung.

## Fitur Utama

-   Komponen dinamis berbasis PHP dan Blade
-   Interaksi real-time tanpa reload halaman
-   Integrasi mudah dengan Alpine.js dan Tailwind CSS
-   Mendukung validasi, file upload, pagination, dan banyak fitur lain
-   SEO friendly (render awal di server)
-   Mudah diintegrasikan dengan aplikasi Laravel

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require livewire/livewire
```

Tambahkan directive Livewire pada layout Blade Anda:

```blade
<head>
    ...
    @livewireStyles
</head>
<body>
    ...
    @livewireScripts
</body>
```

## Membuat Komponen Livewire

Buat komponen baru dengan perintah artisan:

```bash
php artisan make:livewire counter
```

Akan dibuat dua file:

-   `app/Livewire/Counter.php`
-   `resources/views/livewire/counter.blade.php`

### Contoh Komponen Counter

#### Kode PHP:

```php
namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
```

#### Kode Blade:

```blade
<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
</div>
```

## Menampilkan Komponen di Blade

```blade
<livewire:counter />
```

## Dokumentasi Lengkap

-   [Livewire Documentation](https://livewire.laravel.com/docs)
-   [GitHub Livewire](https://github.com/livewire/livewire)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Laravel Livewire._
