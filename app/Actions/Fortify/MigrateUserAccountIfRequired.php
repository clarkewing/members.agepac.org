<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Illuminate\Http\Request;

class MigrateUserAccountIfRequired
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var \Laravel\Fortify\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @param  \Laravel\Fortify\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        $model = $this->guard->getProvider()->getModel();

        $unmigratedUser = $model::where(Fortify::username(), $request->{Fortify::username()})->whereNull('password')->first();

        if (! is_null($unmigratedUser)) {
            Session::put(compact('unmigratedUser'));

            return view('auth.migrate');
        }

        return $next($request);
    }
}
