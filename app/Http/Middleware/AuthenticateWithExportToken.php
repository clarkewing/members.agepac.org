<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Grants access to requests bearing the shared export token, falling back to
 * regular session authentication otherwise. Lets the new app's page import
 * download legacy filemanager files server-to-server without a session.
 */
class AuthenticateWithExportToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = config('services.export.token');

        if ($token && hash_equals($token, (string) $request->bearerToken())) {
            return $next($request);
        }

        return app(Authenticate::class)->handle($request, $next);
    }
}
