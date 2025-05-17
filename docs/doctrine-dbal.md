# Doctrine DBAL

Doctrine DBAL (Database Abstraction Layer) adalah library PHP yang menyediakan lapisan abstraksi database yang kuat, fleksibel, dan aman. DBAL memungkinkan Anda untuk berinteraksi dengan berbagai database menggunakan API yang konsisten tanpa harus menulis query SQL mentah secara langsung.

## Fitur Utama

-   Mendukung banyak database: MySQL, PostgreSQL, SQLite, SQL Server, Oracle, dll
-   Query builder yang aman dari SQL Injection
-   Mendukung transaksi, prepared statement, dan schema introspection
-   Mendukung migrasi dan manajemen skema database
-   Mendukung custom types dan platform

## Instalasi

Jalankan perintah berikut di root project Anda:

```bash
composer require doctrine/dbal
```

## Penggunaan Dasar

### Membuat Koneksi

```php
use Doctrine\DBAL\DriverManager;

$conn = DriverManager::getConnection([
    'dbname' => 'nama_db',
    'user' => 'root',
    'password' => 'password',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
]);
```

### Menjalankan Query

```php
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindValue(1, 1);
$stmt->executeQuery();
$result = $stmt->fetchAllAssociative();
```

### Menggunakan Query Builder

```php
$queryBuilder = $conn->createQueryBuilder();
$queryBuilder
    ->select('u.*')
    ->from('users', 'u')
    ->where('u.id = :id')
    ->setParameter('id', 1);

$result = $queryBuilder->executeQuery()->fetchAllAssociative();
```

### Transaksi

```php
$conn->beginTransaction();
try {
    // operasi database
    $conn->commit();
} catch (\Exception $e) {
    $conn->rollBack();
    throw $e;
}
```

## Dokumentasi Lengkap

-   [Doctrine DBAL Documentation](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/)
-   [GitHub Doctrine DBAL](https://github.com/doctrine/dbal)

---

_Dokumen ini dibuat otomatis berdasarkan sumber resmi Doctrine DBAL._
