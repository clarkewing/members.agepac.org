<?php

namespace App\Providers;

use App\Http\Middleware\Authenticate;
use App\Occupation;
use App\Policies\OccupationPolicy;
use App\Policies\PostPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ThreadPolicy;
use App\Post;
use App\Thread;
use App\User;
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
        Occupation::class => OccupationPolicy::class,
        Thread::class => ThreadPolicy::class,
        Post::class => PostPolicy::class,
        User::class => ProfilePolicy::class,
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
