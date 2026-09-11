<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && is_null(Auth::user()->password_changed_at)) {
            // Exclure les routes de changement et de déconnexion
            if (! $request->routeIs('password.change', 'password.change.update', 'logout')) {
                return redirect()->route('password.change')
                    ->with('warning', 'Vous devez changer votre mot de passe');
            }
        }
        return $next($request);
    }
}