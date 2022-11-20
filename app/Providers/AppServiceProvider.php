<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\NotificationFake;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\Rule;
use Torann\GeoIP\Facades\GeoIP;

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

        NotificationFake::macro('assertSentToVia', function ($notifiable, $channel, $notification) {
            $this->assertSentTo($notifiable, $notification, function ($notification, $channels) use ($channel) {
                return in_array($channel, $channels);
            });
        });

        Paginator::useBootstrap();

        Relation::morphMap([
            'activity' => \App\Models\Activity::class,
            'course' => \App\Models\Course::class,
            'favorite' => \App\Models\Favorite::class,
            'location' => \App\Models\Location::class,
            'occupation' => \App\Models\Occupation::class,
            'post' => \App\Models\Post::class,
            'profile' => \App\Models\Profile::class,
            'thread' => \App\Models\Thread::class,
            'user' => \App\Models\User::class,
        ]);

        Str::macro('nameCase', function (
            string $value,
            array $delimiters = [' ', '-', "O'", "L'", "D'", 'St.', 'Mc'],
            array $lowercaseExceptions = ['the', 'van', 'den', 'von', 'und', 'der', 'de', 'de la', 'da', 'of', 'and', "l'", "d'"],
            array $uppercaseExceptions = ['III', 'IV', 'VI', 'VII', 'VIII', 'IX']
        ) {
            $value = Str::lower($value);

            foreach ($delimiters as $delimiter) {
                $words = explode($delimiter, $value);
                $newWords = [];

                foreach ($words as $word) {
                    if (in_array(Str::upper($word), $uppercaseExceptions)) {
                        $newWords[] = Str::upper($word);
                    } elseif (! in_array($word, $lowercaseExceptions)) {
                        $newWords[] = Str::ucfirst($word);
                    } else {
                        $newWords[] = $word;
                    }
                }

                if (in_array(Str::lower($delimiter), $lowercaseExceptions)) {
                    $delimiter = Str::lower($delimiter);
                }

                $value = implode($delimiter, $newWords);
            }

            return $value;
        });

        Str::macro('feminine', function (
            string $value,
            string $feminine = null,
        ) {
            if (auth()->check() && auth()->user()->gender === 'F') {
                return $feminine ?? $value . 'e';
            }

            return $value;
        });

        Rule::macro('opinionatedPhone', function () {
            return Rule::phone()
                ->detect() // Auto-detect country if country code supplied
                ->country(['FR', GeoIP::getLocation(request()->ip())->iso_code]); // Fallback to France then GeoIP if unable to auto-detect)
        });

        TestResponse::macro('assertPaymentRequired', function () {
            $actual = $this->getStatusCode();

            PHPUnit::assertSame(
                402,
                $actual,
                "Response status code [{$actual}] is not a payment required status code."
            );

            return $this;
        });

        TestResponse::macro('assertNotRedirect', function ($uri = null) {
            if ($this->isRedirect()) {
                if (is_null($uri)) {
                    PHPUnit::fail('Response status code [' . $this->getStatusCode() . '] is a redirect status code.');
                } else {
                    PHPUnit::assertNotEquals(
                        app('url')->to($uri),
                        app('url')->to($this->headers->get('Location'))
                    );
                }

                return $this;
            }

            PHPUnit::assertFalse(
                $this->isRedirect(),
                'Response status code [' . $this->getStatusCode() . '] is a redirect status code.'
            );

            return $this;
        });

        Validator::extend('not_present', function ($attribute, $value, $parameters, $validator) {
            return ! array_key_exists($attribute, $validator->getData());
        });

        Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }
}
