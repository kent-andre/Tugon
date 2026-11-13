<?php

namespace App\Http\Middleware;

use App\Models\Resident;
use Closure;
use Illuminate\Http\Request;

class EnsureResident
{
    public function handle(Request $request, Closure $next)
    {
        $ok = session('resident_logged_in')
            && Resident::whereKey(session('resident.id'))->where('status', 'approved')->exists();

        if (! $ok) {
            $request->session()->forget(['resident_logged_in', 'resident']);
            session(['intended_url' => $request->fullUrl()]);
            return redirect('/login');
        }

        return $next($request);
    }
}
