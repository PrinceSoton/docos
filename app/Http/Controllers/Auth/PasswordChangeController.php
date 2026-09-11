<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.password-change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        // Ajout d'un message de succès (optionnel)
        session()->flash('succes', 'Votre mot de passe a été changé avec succès.');

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'mentor' => redirect()->route('mentor.dashboard'),
            'stagiaire' => redirect()->route('stagiaire.dashboard'),
            default => redirect()->route('login'),
        };
    }
}