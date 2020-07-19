<?php

namespace App\Providers;

use App\Course;
use App\Http\Middleware\Authenticate;
use App\Occupation;
use App\Page;
use App\Policies\CoursePolicy;
use App\Policies\OccupationPolicy;
use App\Policies\PagePolicy;
use App\Policies\PostPolicy;
use App\Policies\ThreadPolicy;
use App\Policies\UserInvitationPolicy;
use App\Policies\UserPolicy;
use App\Post;
use App\Profile;
use App\Thread;
use App\User;
use App\UserInvitation;
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
        Course::class => CoursePolicy::class,
        Occupation::class => OccupationPolicy::class,
        Thread::class => ThreadPolicy::class,
        Page::class => PagePolicy::class,
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
        Profile::class => UserPolicy::class,
        UserInvitation::class => UserInvitationPolicy::class,
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
