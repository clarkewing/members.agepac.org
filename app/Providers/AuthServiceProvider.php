<?php

namespace App\Providers;

use App\Http\Middleware\Authenticate;
use App\Policies\UserPolicy;
use App\Profile;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The explicit policy mappings for the application.
     * All other policies are auto-discovered.
     *
     * @var array
     */
    protected $policies = [
        Profile::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthenticatesRequests::class, Authenticate::class);
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "God with Wings" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('God with Wings') ? true : null;
        });
    }
}
