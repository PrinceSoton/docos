<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Mail\BienvenueStagiaire;
use App\Mail\BienvenueMentor;
use Illuminate\Support\Facades\Mail;

class UserManagementController extends Controller
    {
        public function index()
        {
            $users = User::latest()->paginate(15);
            return view('admin.users.index', compact('users'));
        }

        public function create()
        {
            return view('admin.users.create');
        }

        public function store(Request $request)
        {
            $request->validate([
                'nom'       => 'required|string|max:100',
                'prenom'    => 'required|string|max:100',
                'email'     => 'required|email|unique:users',
                'telephone' => 'nullable|string|max:20',
                'role'      => 'required|in:admin,mentor,stagiaire',
                'password'  => 'required|min:8|confirmed',
                'photo'     => 'nullable|image|max:2048',
            ]);

            $donnees = $request->only('nom', 'prenom', 'email', 'telephone', 'role');
            $donnees['password'] = Hash::make($request->password);
            $donnees['actif']    = true;

            if ($request->hasFile('photo')) {
                $donnees['photo'] = $request->file('photo')->store('photos', 'public');
            }

            $user = User::create($donnees);


            if ($request->role === 'mentor') {
                Mentor::create(['user_id' => $user->id]);
                Mail::to($request->email)->send(new BienvenueMentor($request->only('nom', 'prenom', 'email'), $request->password));
            }

            if ($request->role === 'stagiaire') {
                Stagiaire::create([
                    'user_id'    => $user->id,
                    'matricule'  => Stagiaire::genererMatricule(),
                    'date_debut' => now(),
                    'date_fin'   => now()->addMonths(6),
                    'statut'     => 'en_cours',
                ]);
                Mail::to($request->email)->send(new BienvenueStagiaire($request->only('nom', 'prenom', 'email'), $request->password));
            }


            //$user = User::create($donnees);

            //Mail::to($request->email)->send(new BienvenueStagiaire($request->only('nom', 'prenom', 'email'), $request->password));

            return redirect()->route('admin.users.index')->with('succes', 'Utilisateur créé avec succès.');
        }

        public function show(User $user)
        {
            $user->load('stagiaire', 'mentor');
            return view('admin.users.show', compact('user'));
        }

        public function edit(User $user)
        {
            return view('admin.users.edit', compact('user'));
        }

        public function update(Request $request, User $user)
        {
            $request->validate([
                'nom'       => 'required|string|max:100',
                'prenom'    => 'required|string|max:100',
                'email'     => 'required|email|unique:users,email,' . $user->id,
                'telephone' => 'nullable|string|max:20',
                'role'      => 'required|in:admin,mentor,stagiaire',
                'actif'     => 'boolean',
                'photo'     => 'nullable|image|max:2048',
            ]);

            $donnees = $request->only('nom', 'prenom', 'email', 'telephone', 'role');
            $donnees['actif'] = $request->boolean('actif');

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $donnees['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('photo')) {
                if ($user->photo) Storage::disk('public')->delete($user->photo);
                $donnees['photo'] = $request->file('photo')->store('photos', 'public');
            }

            $user->update($donnees);

            return redirect()->route('admin.users.index')->with('succes', 'Utilisateur mis à jour.');
        }

        public function destroy(User $user)
        {
            // Supprimer les relations avant de supprimer l'utilisateur
    if ($user->stagiaire) {
        $user->stagiaire->delete();
    }
    if ($user->mentor) {
        $user->mentor->delete();
    }
    if ($user->photo) {
        Storage::disk('public')->delete($user->photo);
    }
    $user->delete();

    return redirect()->route('admin.users.index')->with('succes', 'Utilisateur supprimé.');
        }

        public function toggleActif(User $user)
        {
            $user->update(['actif' => !$user->actif]);
            $msg = $user->actif ? 'activé' : 'désactivé';
            return back()->with('succes', "Compte {$msg} avec succès.");
        }
}
