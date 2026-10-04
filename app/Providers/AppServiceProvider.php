<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(SocialAuthServiceProvider::class);
    }

    /**
     * Named rate limiter untuk endpoint autentikasi publik.
     */
    protected function configureRateLimiters(): void
    {
        // Jaring kedua untuk login (limiter berbasis kegagalan ada di AuthController).
        RateLimiter::for('api-login', fn (Request $request) => Limit::perMinute(20)->by('ip:'.$request->ip()));

        // Cegah spam pembuatan order lewat API checkout publik.
        RateLimiter::for('api-checkout', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiters();

        // Konfigurasi Paginator untuk menggunakan Tailwind
        Paginator::useBootstrap();

        // Register Product Observer
        Product::observe(ProductObserver::class);

        // Register Order Observer
        Order::observe(OrderObserver::class);

        $forceScheme = env('FORCE_SCHEME', 'http');
        URL::forceScheme($forceScheme);

        if ($forceScheme === 'https') {
            request()->server->set('HTTP_X_FORWARDED_PROTO', 'https');
        }

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });

        // Mengatur akses ke dokumentasi API
        Gate::define('viewApiDocs', function (?User $user = null) {
            // Selalu minta password dari env
            return request()->hasHeader('PHP_AUTH_PW') &&
                   request()->header('PHP_AUTH_PW') === env('SCRAMBLE_DOCS_PASSWORD');
        });
    }
}
