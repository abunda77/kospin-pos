# Project Structure

## Laravel Application Organization

### Core Application (`app/`)
```
app/
├── Exports/           # Excel export classes
├── Filament/          # Admin panel resources, pages, widgets
├── Http/              # Controllers, middleware, requests
├── Imports/           # Excel import classes  
├── Livewire/          # Real-time UI components (POS, cart, search)
├── Models/            # Eloquent models (Product, Order, User, etc.)
├── Observers/         # Model event observers
├── Policies/          # Authorization policies
├── Providers/         # Service providers
├── Services/          # Business logic services (Payment, ImageOptimizer)
└── helpers.php        # Global helper functions
```

### Configuration (`config/`)
- Standard Laravel config files
- Key configs: `filament.php`, `permission.php`, `excel.php`, `scramble.php`

### Database (`database/`)
```
database/
├── factories/         # Model factories for testing
├── migrations/        # Database schema migrations
└── seeders/          # Database seeders
```

### Frontend Assets (`resources/`)
```
resources/
├── css/              # Stylesheets (TailwindCSS)
├── js/               # JavaScript files (Alpine.js)
└── views/            # Blade templates
```

### Public Assets (`public/`)
```
public/
├── build/            # Compiled Vite assets
├── css/              # Additional stylesheets
├── images/           # Static images
├── js/               # Additional JavaScript
└── storage/          # Symlinked storage files
```

## Key Models & Relationships

### Core Business Models
- **Product** → belongs to Category, has many OrderProducts, Images
- **Category** → has many Products
- **Order** → belongs to User/Anggota, has many OrderProducts, belongs to PaymentMethod
- **OrderProduct** → belongs to Order and Product
- **User** → has many Orders, uses Spatie Permissions
- **Anggota** (Members) → has many Orders

### Supporting Models
- **PaymentMethod** → has many Orders, supports gateway integration
- **VoucherDiskon** → discount/voucher system
- **BannerIklan** → promotional banners
- **Expense** → expense tracking
- **Setting** → application configuration
- **Image** → file management
- **BackupLog** → backup tracking
- **Quote** → quotation system

## Filament Admin Structure
```
app/Filament/
├── Pages/            # Custom admin pages
├── Resources/        # CRUD resources for models
└── Widgets/          # Dashboard widgets and charts
```

## Livewire Components
- **Pos.php** → Main POS interface
- **AddToCart.php** → Cart functionality
- **ProductSearch.php** → Product search
- **CartItems.php** → Cart management
- **ScannerModalComponent.php** → QR/Barcode scanning

## File Naming Conventions
- **Models**: PascalCase (Product.php, OrderProduct.php)
- **Controllers**: PascalCase with Controller suffix
- **Livewire**: PascalCase (AddToCart.php)
- **Migrations**: snake_case with timestamp prefix
- **Views**: kebab-case.blade.php
- **Config files**: snake_case.php

## Important Files
- **kasir.sql** → Database dump for initial setup
- **app/helpers.php** → Global helper functions
- **.env.example** → Environment configuration template
- **tailwind.config.js** → TailwindCSS configuration
- **vite.config.js** → Frontend build configuration

## Storage Organization
- **storage/app/public/** → User uploaded files (linked to public/storage)
- **storage/logs/** → Application logs
- **storage/framework/** → Framework cache and sessions