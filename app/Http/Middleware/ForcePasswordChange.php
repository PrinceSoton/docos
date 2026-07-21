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
        // Uniquement pour ceux qui n'ont pas changé de mot de passe
        if (Auth::check() && is_null(Auth::user()->password_changed_at)) {

            if (! $request->routeIs('password.change', 'password.update', 'logout')) {
                return redirect()->route('password.change')
                    ->with('warning', 'Vous devez changer votre mot de passe');
            }
        }

        return $next($request);
    }
}
