<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('spamfree', 'App\Rules\SpamFree@passes');

        Arr::macro('keysFromValues', function (array $array) {
            return array_combine($array, $array);
        });

        Builder::macro('concat', function (...$elements) {
            $dbConnection = config('database.default');
            $dbDriver = config("database.connections.$dbConnection.driver");

            switch ($dbDriver) {
                case 'mysql':
                    return DB::raw('CONCAT(' . implode(', ', $elements) . ')');

                case 'sqlite':
                    return DB::raw(implode(' || ', $elements));
            }

            throw new \Exception('Concat macro not defined for the current database driver.');
        });
    }
}
