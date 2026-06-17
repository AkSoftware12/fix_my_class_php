<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks tenant-level users whose coaching subscription / tenancy chain
 * has been deactivated, and shares the tenant on the container for reuse.
 */
class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasRole('Super Admin')) {
            $coaching = $user->coaching;

            if ($coaching && ! $coaching->is_active) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'This coaching account is suspended.'], 403);
                }

                abort(403, 'This coaching account is suspended.');
            }

            app()->instance('tenant.coaching', $coaching);
        }

        return $next($request);
    }
}
