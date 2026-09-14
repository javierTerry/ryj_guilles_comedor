<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('manage-free-bookings', function (User $user) {
            return $user->isSuperAdmin()
                || $user->isAdmin()
                || $user->hasRole('admin')
                || $user->hasRole('super_admin')
                || $user->hasRole('super-admin');
        });
    }
}
