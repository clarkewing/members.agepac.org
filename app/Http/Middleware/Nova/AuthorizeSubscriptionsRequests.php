<?php

namespace App\Http\Middleware\Nova;

use Spatie\Permission\Exceptions\UnauthorizedException;

class AuthorizeSubscriptionsRequests
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     * @throws \Spatie\Permission\Exceptions\UnauthorizedException|\Throwable
     */
    public function handle($request, $next)
    {
        if (
            $request->segment(1) === 'nova-vendor'
            && $request->segment(2) === 'nova-cashier-overview'
        ) {
            throw_unless(
                $request->user()->hasPermissionTo('subscriptions.manage'),
                UnauthorizedException::forPermissions(['subscriptions.manage'])
            );
        }

        return $next($request);
    }
}
