<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCheck
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($role && Auth::user()->role !== $role) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}