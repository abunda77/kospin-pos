<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class EnsureSocialUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Ensure we have a User model instance with HasRoles trait
            if ($user instanceof \App\Models\User) {
                // Force load roles relationship
                $user->load('roles');

                // Ensure user has at least the 'user' role
                if ($user->roles->isEmpty()) {
                    $userRole = Role::firstOrCreate([
                        'name' => 'user',
                        'guard_name' => 'web'
                    ]);

                    $user->assignRole($userRole);

                    // Clear permission cache
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                    Log::info('Auto-assigned user role in middleware', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                }
            }
        }

        return $next($request);
    }
}
