<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Used in routes as ->middleware('role:captain,secretary'). Runs after EnsureAdmin.
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array(session('admin.role'), $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account does not have access to this action.'], 403);
            }
            abort(403, 'Your account does not have access to this page.');
        }

        return $next($request);
    }
}
