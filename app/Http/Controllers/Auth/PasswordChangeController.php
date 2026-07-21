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
            'current_password' => ['required', 'current_password'], // Validation du mot de passe
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_changed_at = now(); //Changement d'un nouveau mot de passe
        $user->save();

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'mentor' => redirect()->route('mentor.dashboard'),
            'stagiaire' => redirect()->route('stagiaire.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
