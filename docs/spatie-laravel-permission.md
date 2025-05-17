# Spatie Laravel Permission

Spatie Laravel Permission adalah package Laravel yang memungkinkan Anda mengelola peran (role) dan izin (permission) pengguna secara mudah dan fleksibel menggunakan database. Sangat cocok untuk aplikasi yang membutuhkan kontrol akses berbasis role dan permission.

## Fitur Utama

-   Manajemen role dan permission berbasis database
-   Mendukung multiple guard
-   Integrasi dengan Laravel Gate dan Blade directives
-   Mendukung middleware untuk proteksi route
-   API yang mudah digunakan dan didokumentasikan

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

Tambahkan trait `HasRoles` pada model User Anda:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

## Penggunaan Dasar

### Membuat Role dan Permission

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::create(['name' => 'writer']);
$permission = Permission::create(['name' => 'edit articles']);
```

### Memberikan Permission ke Role

```php
$role->givePermissionTo('edit articles');
```

### Memberikan Role ke User

```php
$user->assignRole('writer');
```

### Memberikan Permission langsung ke User

```php
$user->givePermissionTo('edit articles');
```

### Mengecek Permission

```php
$user->can('edit articles'); // true/false
```

### Blade Directive

```blade
@can('edit articles')
    <!-- Konten khusus user dengan izin -->
@endcan
```

### Mendapatkan Daftar Role dan Permission User

```php
$user->getRoleNames(); // Collection nama role
$user->getPermissionNames(); // Collection nama permission
```

## Dokumentasi Lengkap

-   [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission/v6/introduction)
-   [GitHub Spatie Laravel Permission](https://github.com/spatie/laravel-permission)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Spatie Laravel Permission._
