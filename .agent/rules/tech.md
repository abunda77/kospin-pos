---
trigger: always_on
---

# Technology Stack

## Backend Framework
- **PHP**: ^8.2 (minimum requirement)
- **Laravel**: ^11.9 (latest Laravel framework)
- **Laravel Octane**: ^2.6 (high-performance application server)

## Admin Panel & UI
- **Filament**: ^3.2 (admin panel framework)
- **Filament Shield**: ^3.3 (role-based access control)
- **Livewire**: Real-time UI components
- **TailwindCSS**: ^3.4.1 (utility-first CSS framework)
- **Alpine.js**: ^3.14.9 (lightweight JavaScript framework)

## Key Libraries & Services
- **Authentication**: Laravel Sanctum ^4.0 (API tokens)
- **Permissions**: Spatie Laravel Permission ^6.10
- **PDF Generation**: Barryvdh Laravel DomPDF ^3.1
- **Thermal Printing**: Mike42 ESC/POS PHP ^4.0
- **Image Processing**: Intervention Image ^3.11
- **Excel Operations**: Maatwebsite Excel ^3.1
- **Barcode Generation**: Picqer PHP Barcode Generator ^3.2
- **Payment Gateways**: Midtrans PHP ^2.6, Xendit PHP ^7.0
- **API Documentation**: Dedoc Scramble ^0.11.33
- **Data Trending**: Flowframe Laravel Trend ^0.3.0
- **Backup**: Spatie Laravel Backup ^9.2

## Frontend Build Tools
- **Vite**: ^5.0 (build tool and dev server)
- **PostCSS**: ^8.4.33 with Autoprefixer ^10.4.17
- **Axios**: ^1.6.4 (HTTP client)

## Development Tools
- **Laravel Pint**: ^1.13 (code style fixer)
- **PHPUnit**: ^11.0.1 (testing framework)
- **Laravel Sail**: ^1.26 (Docker development environment)

## Common Commands

### Development
```bash
# Start development server
php artisan serve

# Start frontend development
npm run dev

# Build for production
npm run build

# Link storage
php artisan storage:link
```

### Database
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Maintenance
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate application key
php artisan key:generate

# Run backups
php artisan backup:run
```

### Code Quality
```bash
# Fix code style
./vendor/bin/pint

# Run tests
php artisan test
```

## Environment Requirements
- **Local Server**: Laragon (recommended for Windows)
- **Composer**: PHP dependency manager
- **Node.js**: For frontend asset compilation
- **Git**: Version control
- **Thermal Printer**: 58mm ESC/POS compatible (optional)
- **QR Scanner**: Camera or dedicated scanner (optional)