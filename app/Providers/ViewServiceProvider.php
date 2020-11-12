<?php

namespace App\Providers;

use App\Channel;
use App\Trending;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

        View::composer('*', function ($view) {
            $view->with('listOfDays', Cache::rememberForever('listOfDays', function () {
                return range(1, 31);
            }));

            $view->with('listOfMonths', Cache::rememberForever('listOfMonths', function () {
                return collect(range(1, 12))->mapWithKeys(function ($monthNum) {
                    return [
                        $monthNum => Str::title(
                            Carbon::createFromFormat('m', $monthNum)
                                ->isoFormat('MMMM')
                        )
                    ];
                })->all();
            }));

            $view->with('listOfYears', Cache::remember('listOfYears', today()->addYear()->startOfYear(), function () {
                return range(today()->year - 13, today()->year - 100);
            }));
        });
    }
}
