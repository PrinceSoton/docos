<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'photo'     => 'nullable|image|max:2048',
        ]);

        $donnees = $request->only('nom', 'prenom', 'email', 'telephone');

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $donnees['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $user->update($donnees);

        return redirect()->route('profile.index')->with('succes', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'ancien_mot_de_passe' => 'required',
            'password'            => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->ancien_mot_de_passe, $user->password)) {
            return back()->withErrors(['ancien_mot_de_passe' => 'Mot de passe actuel incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profile.index')->with('succes', 'Mot de passe modifié avec succès.');
    }
}