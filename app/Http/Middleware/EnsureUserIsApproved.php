<?php

namespace App\Http\Middleware;

use App\Exceptions\PendingApprovalException;
use Closure;
use Illuminate\Http\Request;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->isApproved()) {
            $this->pendingApproval($request);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not yet approved.
     */
    protected function redirectTo(Request $request): string
    {
        if (! $request->expectsJson()) {
            return route('pending-approval');
        }
    }

    /**
     * Handle a user pending approval.
     */
    protected function pendingApproval(Request $request)
    {
        throw new PendingApprovalException(
            redirectTo: $this->redirectTo($request)
        );
    }
}
