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
        // Backend currently issues the auth cookie as `token`.
        // Accept the legacy `zaku_token` name too for existing sessions.
        $token = $request->cookie('token') ?: $request->cookie('zaku_token');

        if (! $token || $token === '') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
