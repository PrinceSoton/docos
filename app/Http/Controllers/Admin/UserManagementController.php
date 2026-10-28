<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
            $mentors = Mentor::with('user')->get();
            return view('admin.users.create', compact('mentors'));
        }

        public function store(Request $request)
        {
            $rules = [
                'nom'       => 'required|string|max:100',
                'prenom'    => 'required|string|max:100',
                'email'     => 'required|email|unique:users',
                'telephone' => 'nullable|string|max:20',
                'role'      => 'required|in:admin,mentor,stagiaire',
                'password'  => 'required|min:8|confirmed',
                'photo'     => 'nullable|image|max:2048',
            ];

            if ($request->input('role') === 'mentor') {
                $rules += [
                    'departement' => 'nullable|string|max:100',
                    'poste'       => 'nullable|string|max:100',
                    'bio'         => 'nullable|string',
                ];
            }

            if ($request->input('role') === 'stagiaire') {
                $rules += [
                    'ecole'        => 'nullable|string|max:150',
                    'specialite'   => 'nullable|string|max:150',
                    'niveau_etude' => 'nullable|string|max:100',
                    'date_debut'   => 'required|date',
                    'date_fin'     => 'required|date|after:date_debut',
                    'mentor_id'    => ['nullable', Rule::exists('users', 'id')->where('role', 'mentor')],
                    'description'  => 'nullable|string',
                    'cv'           => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                ];
            }

            $validated = $request->validate($rules);

            $donnees = $request->only('nom', 'prenom', 'email', 'telephone', 'role');
            $donnees['password'] = Hash::make($request->password);
            $donnees['actif']    = true;

            $photoPath = null;
            $cvPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public');
                $donnees['photo'] = $photoPath;
            }

            if ($validated['role'] === 'stagiaire' && $request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('cvs', 'public');
            }

            try {
                DB::transaction(function () use ($donnees, $validated, $cvPath) {
                    $user = User::create($donnees);

                    if ($validated['role'] === 'mentor') {
                        Mentor::create([
                            'user_id'     => $user->id,
                            'departement' => $validated['departement'] ?? null,
                            'poste'       => $validated['poste'] ?? null,
                            'bio'         => $validated['bio'] ?? null,
                        ]);
                    }

                    if ($validated['role'] === 'stagiaire') {
                        Stagiaire::create([
                            'user_id'      => $user->id,
                            'matricule'    => Stagiaire::genererMatricule(),
                            'ecole'        => $validated['ecole'] ?? null,
                            'specialite'   => $validated['specialite'] ?? null,
                            'niveau_etude' => $validated['niveau_etude'] ?? null,
                            'date_debut'   => $validated['date_debut'],
                            'date_fin'     => $validated['date_fin'],
                            'mentor_id'    => $validated['mentor_id'] ?? null,
                            'description'  => $validated['description'] ?? null,
                            'cv'           => $cvPath,
                            'statut'       => 'en_cours',
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                if ($photoPath) Storage::disk('public')->delete($photoPath);
                if ($cvPath) Storage::disk('public')->delete($cvPath);
                throw $e;
            }

            if ($validated['role'] === 'mentor') {
                Mail::to($request->email)->send(new BienvenueMentor($request->only('nom', 'prenom', 'email'), $request->password));
            }

            if ($validated['role'] === 'stagiaire') {
                Mail::to($request->email)->send(new BienvenueStagiaire($request->only('nom', 'prenom', 'email'), $request->password));
            }

            return redirect()->route('admin.users.index')->with('succes', 'Utilisateur créé avec succès.');
        }

        public function show(User $user)
        {
            $user->load('stagiaire', 'mentor');
            return view('admin.users.show', compact('user'));
        }

        public function edit(User $user)
        {
            $user->load('mentor', 'stagiaire');
            $mentors = Mentor::with('user')->get();
            return view('admin.users.edit', compact('user', 'mentors'));
        }

        public function update(Request $request, User $user)
        {
            $rules = [
                'nom'       => 'required|string|max:100',
                'prenom'    => 'required|string|max:100',
                'email'     => 'required|email|unique:users,email,' . $user->id,
                'telephone' => 'nullable|string|max:20',
                'role'      => 'required|in:admin,mentor,stagiaire',
                'actif'     => 'boolean',
                'photo'     => 'nullable|image|max:2048',
                'password'  => 'nullable|min:8|confirmed',
            ];

            if ($request->input('role') === 'mentor') {
                $rules += [
                    'departement' => 'nullable|string|max:100',
                    'poste' => 'nullable|string|max:100',
                    'bio' => 'nullable|string',
                ];
            }

            if ($request->input('role') === 'stagiaire') {
                // Une modification d'identité conserve les dates si elles ne sont pas envoyées.
                $request->merge([
                    'date_debut' => $request->input('date_debut', $user->stagiaire?->date_debut?->format('Y-m-d')),
                    'date_fin' => $request->input('date_fin', $user->stagiaire?->date_fin?->format('Y-m-d')),
                ]);
                $rules += [
                    'ecole' => 'nullable|string|max:150',
                    'specialite' => 'nullable|string|max:150',
                    'niveau_etude' => 'nullable|string|max:100',
                    'date_debut' => 'required|date',
                    'date_fin' => 'required|date|after:date_debut',
                    'mentor_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'mentor')],
                    'description' => 'nullable|string',
                    'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                ];
            }

            $request->validate($rules);

            $donnees = $request->only('nom', 'prenom', 'email', 'telephone', 'role');
            $donnees['actif'] = $request->boolean('actif');

            if ($request->filled('password')) {
                $donnees['password'] = Hash::make($request->password);
            }

            $photoPath = null;
            $cvPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public');
                $donnees['photo'] = $photoPath;
            }
            if ($request->input('role') === 'stagiaire' && $request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('cvs', 'public');
            }

            $anciennePhoto = $user->photo;
            $ancienCv = $user->stagiaire?->cv;
            try {
                DB::transaction(function () use ($request, $user, $donnees, $cvPath) {
                    $user->update($donnees);
                    if ($user->role === 'mentor') {
                        $mentor = $user->mentor()->firstOrNew();
                        $mentor->fill($request->only('departement', 'poste', 'bio'));
                        $mentor->save();
                    }
                    if ($user->role === 'stagiaire') {
                        $stagiaire = $user->stagiaire()->firstOrNew();
                        if (!$stagiaire->exists) {
                            $stagiaire->matricule = Stagiaire::genererMatricule();
                            $stagiaire->statut = 'en_cours';
                        }
                        $stagiaire->fill($request->only('ecole', 'specialite', 'niveau_etude', 'date_debut', 'date_fin', 'mentor_id', 'description'));
                        if ($cvPath) $stagiaire->cv = $cvPath;
                        $stagiaire->save();
                    }
                });
            } catch (\Throwable $e) {
                if ($photoPath) Storage::disk('public')->delete($photoPath);
                if ($cvPath) Storage::disk('public')->delete($cvPath);
                throw $e;
            }
            if ($photoPath && $anciennePhoto) Storage::disk('public')->delete($anciennePhoto);
            if ($cvPath && $ancienCv) Storage::disk('public')->delete($ancienCv);

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
