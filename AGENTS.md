# AGENTS.md

## Stack & entrypoints
- Laravel 11 / PHP 8.3 POS; the primary public storefront and checkout routes are in `routes/web.php`, while Sanctum API routes and the Midtrans webhook are in `routes/api.php`.
- The Filament admin panel is `/admin`; its discovery, middleware, Shield authorization, and social-login plugins are configured in `app/Providers/Filament/AdminPanelProvider.php`. Add admin CRUD through `app/Filament/Resources`, not custom web routes.
- Public catalog/cart behavior is session-backed and uses `app/Livewire` components with Blade views in `resources/views`; preserve the `view_preference` mobile/desktop session behavior.
- Payment gateways must use `PaymentGatewayFactory` and `PaymentGatewayInterface` in `app/Services/Payment`; supported gateway keys are `midtrans` and `xendit`.

## Domain constraints
- `Order` uses UUID primary keys. Generate `no_order` only with `Order::generateNextOrderNumber()` inside `DB::transaction()`; it relies on `SELECT ... FOR UPDATE` for uniqueness.
- Creating an `Order` triggers `OrderObserver`, which sends `WEBHOOK_URL` notifications when configured. Saving a `Product` triggers image optimization. Creating `OrderProduct` triggers its observer; account for these side effects in application code and tests.
- Payment, social-login, and API documentation credentials are environment-configured. Never place live credentials in source or test fixtures.

## Commands & verification
- Install/configure dependencies, database, then run `php artisan storage:link` for publicly served product and QRIS images.
- Backend: `php artisan serve`; frontend assets: `npm run dev`; production assets: `npm run build`. Vite builds `resources/css/app.css` and `resources/js/app.js`.
- Format modified PHP with `vendor/bin/pint --dirty --format agent`.
- Tests are PHPUnit: run a focused test with `php artisan test --compact tests/Feature/OrderNumberGenerationTest.php` or `php artisan test --compact --filter=testName`; run all with `php artisan test --compact`.
- Tests use the configured database: the SQLite test overrides are disabled in `phpunit.xml`. Provide a disposable migrated database before running tests that use `RefreshDatabase`.

## Framework specifics
- Register application providers in `bootstrap/providers.php`; routing and global middleware are configured in `bootstrap/app.php`.
- This app may run with Octane. Do not retain request-specific state in singletons or static properties.
- Do not add dependencies or new top-level application directories without approval.
