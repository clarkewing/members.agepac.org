<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Paginator;
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

        Paginator::useBootstrap();

        Relation::morphMap([
            'activity' => 'App\Models\Activity',
            'course' => 'App\Models\Course',
            'favorite' => 'App\Models\Favorite',
            'location' => 'App\Models\Location',
            'occupation' => 'App\Models\Occupation',
            'post' => 'App\Models\Post',
            'profile' => 'App\Models\Profile',
            'thread' => 'App\Models\Thread',
            'user' => 'App\Models\User',
        ]);

        Validator::extend('not_present', function ($attribute, $value, $parameters, $validator) {
            return ! array_key_exists($attribute, $validator->getData());
        });

        Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }
}
