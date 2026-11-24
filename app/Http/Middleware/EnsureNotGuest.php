<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotGuest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isGuest()) {
            // Rute yang diizinkan untuk Guest:
            // guest.*, logout, profile.*, switch-role
            if (
                $request->routeIs('guest.*') || 
                $request->routeIs('logout') || 
                $request->routeIs('profile.*') || 
                $request->routeIs('switch-role') ||
                $request->routeIs('notifications.*')
            ) {
                return $next($request);
            }

            return redirect()->route('guest.dashboard')
                ->with('warning', 'Akses Dibatasi: Akun Anda saat ini berstatus Guest. Administrator sedang meninjau dan menetapkan hak akses peran untuk akun Anda.');
        }

        return $next($request);
    }
}
