<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JwtSession
{
    /**
     * Check JWT token cookie to protect Blade routes.
     * Real auth validation happens on the backend API.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('zaku_token');

        if (! $token || $token === '') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
