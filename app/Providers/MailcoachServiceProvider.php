<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class MailcoachServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        Gate::define('viewMailcoach', function ($user) {
            return $user->hasPermissionTo('viewMailcoachDashboard');
        });
    }
}
