<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses ditolak!');
    }
}
