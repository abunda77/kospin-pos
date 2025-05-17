# Laravel Excel (maatwebsite/excel)

Laravel Excel adalah package Laravel yang sangat powerful untuk melakukan ekspor dan impor data Excel (XLS, XLSX, CSV) secara mudah dan efisien. Cocok untuk aplikasi yang membutuhkan fitur ekspor/impor data dalam format spreadsheet.

## Fitur Utama

-   Ekspor data Eloquent, Collection, atau View ke file Excel/CSV
-   Impor data dari file Excel/CSV ke model Eloquent
-   Mendukung chunking, queue, dan batch untuk performa tinggi
-   Integrasi dengan Laravel Nova
-   Mendukung berbagai format: XLSX, XLS, CSV, ODS, TSV
-   API yang mudah digunakan dan didokumentasikan

## Instalasi

Jalankan perintah berikut di root project Laravel Anda:

```bash
composer require maatwebsite/excel
```

## Ekspor Data ke Excel

Buat class export:

```php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    public function collection()
    {
        return User::all();
    }
}
```

Ekspor ke file Excel:

```php
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

return Excel::download(new UsersExport, 'users.xlsx');
```

## Impor Data dari Excel

Buat class import:

```php
namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    public function model(array $row)
    {
        return new User([
            'email' => $row[1],
        ]);
    }
}
```

Impor file Excel:

```php
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

Excel::import(new UsersImport, 'users.xlsx');
```

## Dokumentasi Lengkap

-   [Laravel Excel Documentation](https://docs.laravel-excel.com/)
-   [GitHub Laravel Excel](https://github.com/SpartnerNL/Laravel-Excel)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Laravel Excel (maatwebsite/excel)._
