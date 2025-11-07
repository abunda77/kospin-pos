<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentSocial\Events\SocialLogin;
use TomatoPHP\FilamentSocial\Events\SocialRegister;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;

class SocialAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Event::listen(SocialLogin::class, function ($event) {
            // Your logic for social login event
            // $event->user contains the authenticated user
            // $event->provider contains the social provider name
        });

        Event::listen(SocialRegister::class, function ($event) {
            // Social register event logic
            $user = $event->user;
            $provider = $event->provider;

            // Assign default role to the user if roles are used
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('user'); // or 'admin' if appropriate
            }

            // Log in the user after registration
            Auth::login($user);
        });
    }
}