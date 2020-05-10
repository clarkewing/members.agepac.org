<?php

namespace App\Providers;

use App\Channel;
use App\Trending;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('threads.*', function ($view) {
            $view->with('channels', Cache::rememberForever('channels', function () {
                return Channel::all();
            }));

            $view->with('trending', (new Trending)->get());
        });
    }
}
