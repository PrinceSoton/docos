<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('Les identifiants sont incorrects.'),
            ]);
        }

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