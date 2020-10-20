<?php

namespace App\Providers;

use App\Channel;
use App\Http\Controllers\PagesController;
use App\Post;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::bind('channel', function ($value) {
            return Channel::withoutGlobalScopes()->where('slug', $value)->firstOrFail();
        });

        Route::bind('post', function ($value) {
            if (Auth::user()->can('restore', Post::class)
                || Auth::user()->can('forceDelete', Post::class)) {
                return Post::withTrashed()->findOrFail($value);
            }

            return Post::findOrFail($value);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapDynamicPagesRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "pages" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapDynamicPagesRoutes()
    {
        Route::get('pages/{page}', [PagesController::class, 'show'])
            ->where('page', '[a-z0-9]+([a-z0-9-\/][a-z0-9]+)*')
            ->middleware('web')
            ->name('pages.show');
    }
}
