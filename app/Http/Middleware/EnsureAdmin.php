<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Read the account on every request, so a disabled account is logged out right away.
        $admin = session('admin_logged_in') ? Admin::find(session('admin.id')) : null;

        if (! $admin || ! $admin->is_active) {
            $request->session()->forget(['admin_logged_in', 'admin']);

            // AJAX calls (Save Changes) get a JSON 401 instead of a redirect.
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session expired. Please log in again.'], 401);
            }
            if ($request->isMethod('GET')) {
                session(['admin_intended_url' => $request->fullUrl()]);
            }
            return redirect('/admin-login');
        }

        // Keeps name and role current if the Punong Barangay changed them.
        session(['admin' => $admin->sessionData()]);
        View::share('me', session('admin'));

        $response = $next($request);
        // Stop the browser's Back button from showing admin pages after logout.
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
