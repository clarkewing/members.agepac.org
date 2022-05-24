<?php

namespace App\Http\Middleware\Nova;

use OptimistDigital\MenuBuilder\MenuBuilder;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AuthorizeMenusRequests
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
            && $request->segment(2) === MenuBuilder::getMenuResource()::uriKey()
        ) {
            throw_unless(
                $request->user()->hasPermissionTo('menus.manage'),
                UnauthorizedException::forPermissions(['menus.manage'])
            );
        }

        return $next($request);
    }
}
