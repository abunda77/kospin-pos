<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Konfigurasi Paginator untuk menggunakan Tailwind
        Paginator::useBootstrap();

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
        Gate::define('viewApiDocs', function () {
                // Selalu minta password dari env
                return request()->hasHeader('PHP_AUTH_PW') &&
                       request()->header('PHP_AUTH_PW') === env('SCRAMBLE_DOCS_PASSWORD');
            });
    }
}
