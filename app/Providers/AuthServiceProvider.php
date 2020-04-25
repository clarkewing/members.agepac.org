<?php

namespace App\Providers;

use App\Http\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Thread' => 'App\Policies\ThreadPolicy',
        'App\Reply' => 'App\Policies\ReplyPolicy',
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

        // Super-admin authorization
        Gate::before(function ($user) {
            if ($user->name === 'John Doe') {
                return true;
            }
        });
    }
}
