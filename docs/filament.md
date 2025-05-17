# Filament

Filament adalah framework Laravel modern untuk membangun admin panel, dashboard, dan aplikasi CRUD dengan cepat, mudah, dan tampilan yang indah. Filament menyediakan komponen siap pakai seperti Panel Builder, Form Builder, Table Builder, Notification, Widget, dan lainnya, serta dibangun di atas TALL Stack (Tailwind, Alpine.js, Laravel, Livewire).

## Fitur Utama

-   Panel admin siap pakai dan dapat dikustomisasi
-   Form Builder dengan lebih dari 25 komponen
-   Table Builder untuk datatable interaktif
-   Widget dashboard (statistik, chart, dsb)
-   Notifikasi real-time
-   Multi-tenancy, multi-panel, dan plugin
-   Integrasi dengan Laravel Policies, Gate, dan Permission
-   Dukungan tema dan kustomisasi tampilan

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require filament/filament
php artisan filament:install
```

## Contoh Penggunaan

### Membuat Resource CRUD

```bash
php artisan make:filament-resource Post
```

### Contoh Resource

```php
namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Forms;
use Filament\Resources\Tables;

class PostResource extends Resource
{
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Textarea::make('content'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('created_at')->date(),
        ]);
    }
}
```

### Proteksi Akses Admin Panel

Implementasikan kontrak `FilamentUser` pada model User:

```php
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    public function canAccessFilament(): bool
    {
        return $this->hasVerifiedEmail();
    }
}
```

## Dokumentasi Lengkap

-   [Filament Documentation](https://filamentphp.com/docs)
-   [GitHub Filament](https://github.com/filamentphp/filament)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Filament._
