<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModule
{
    /**
     * Handle an incoming request.
     *
     * Usage: Route::middleware('module:sales')
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins bypass all module checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->hasModule($module)) {
            abort(403, 'You do not have access to this module.');
        }

        return $next($request);
    }
}
