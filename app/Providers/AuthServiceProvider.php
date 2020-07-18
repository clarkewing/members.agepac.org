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
use App\Policies\ProfilePolicy;
use App\Policies\ThreadPolicy;
use App\Post;
use App\Thread;
use App\User;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
    }
}
