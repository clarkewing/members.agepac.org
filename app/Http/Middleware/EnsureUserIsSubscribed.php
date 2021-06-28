<?php

namespace App\Http\Middleware;

use App\Exceptions\UnsubscribedException;
use Closure;
use Illuminate\Http\Request;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws \App\Exceptions\UnsubscribedException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->subscribed('default')) {
            $this->unsubscribed($request);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not subscribed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('subscription.edit');
        }
    }

    /**
     * Handle an unsubscribed user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @throws \App\Exceptions\UnsubscribedException
     */
    protected function unsubscribed(Request $request)
    {
        throw new UnsubscribedException(
            'Subscription inactive.',
            $this->redirectTo($request)
        );
    }
}
