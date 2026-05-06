<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        foreach ($permissions as $permission) {
            if (!auth()->user()->hasPermissionTo($permission) && !auth()->user()->hasRole($permission)) {
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
