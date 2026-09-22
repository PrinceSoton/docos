<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        if (!Auth::user()->actif) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Votre compte est désactivé. Contactez l\'administrateur.'),
            ]);
        }

        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'mentor'    => redirect()->route('mentor.dashboard'),
            'stagiaire' => redirect()->route('stagiaire.dashboard'),
            default     => redirect('/'),
        };
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}