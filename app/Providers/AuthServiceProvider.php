<?php

namespace App\Providers;

use App\Http\Middleware\Authenticate;
use App\Models\Profile;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

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

        Gate::define('viewNova', function (User $user) {
            return $user->getAllPermissions()->count();
        });

        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\EnsureUserIsSubscribed::class,
        ]);
    }
}
