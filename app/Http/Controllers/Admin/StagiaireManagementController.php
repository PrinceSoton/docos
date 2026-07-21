<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\User;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Mail\BienvenueStagiaire;
use Illuminate\Support\Facades\Mail;

class StagiaireManagementController extends Controller
{
    public function index()
    {
        $stagiaires = Stagiaire::with('user', 'mentor')->latest()->paginate(15);
        return view('admin.stagiaires.index', compact('stagiaires'));
    }

    public function create()
    {
        $mentors = Mentor::with('user')->get();
       return view('admin.stagiaires.create', compact('mentors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8|confirmed',
            'telephone'   => 'nullable|string|max:20',
            'ecole'       => 'nullable|string|max:150',
            'specialite'  => 'nullable|string|max:150',
            'niveau_etude'=> 'nullable|string|max:100',
            'date_debut'  => 'required|date',
            'date_fin'    => 'required|date|after:date_debut',
            'mentor_id'   => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'cv'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'nom'      => $request->nom,
            'prenom'   => $request->prenom,
            'email'    => $request->email,
            'telephone'=> $request->telephone,
            'password' => Hash::make($request->password),
            'role'     => 'stagiaire',
            'actif'    => true,
            'photo'    => $photoPath,
        ]);

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        Stagiaire::create([
            'user_id'      => $user->id,
            'matricule'    => Stagiaire::genererMatricule(),
            'ecole'        => $request->ecole,
            'specialite'   => $request->specialite,
            'niveau_etude' => $request->niveau_etude,
            'date_debut'   => $request->date_debut,
            'date_fin'     => $request->date_fin,
            'mentor_id'    => $request->mentor_id,
            'description'  => $request->description,
            'cv'           => $cvPath,
            'statut'       => 'en_cours',
        ]);

         Mail::to($request->email)->send(new BienvenueStagiaire($request->only('nom', 'prenom', 'email'), $request->password));


        return redirect()->route('admin.stagiaires.index')->with('succes', 'Stagiaire créé avec succès.');
    }

    public function show(Stagiaire $stagiaire)
    {
        $stagiaire->load('user', 'mentor', 'presences', 'reports', 'projects', 'tasks', 'attestations');
        return view('admin.stagiaires.show', compact('stagiaire'));
    }

    public function edit(Stagiaire $stagiaire)
    {
        $mentors = Mentor::with('user')->get();
        return view('admin.stagiaires.edit', compact('stagiaire', 'mentors'));
    }

    public function update(Request $request, Stagiaire $stagiaire)
    {
        $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $stagiaire->user_id,
            'ecole'       => 'nullable|string|max:150',
            'specialite'  => 'nullable|string|max:150',
            'niveau_etude'=> 'nullable|string|max:100',
            'date_debut'  => 'required|date',
            'date_fin'    => 'required|date|after:date_debut',
            'mentor_id'   => 'nullable|exists:users,id',
            'statut'      => 'required|in:en_cours,termine,suspendu',
            'description' => 'nullable|string',
            'cv'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $donneesUser = $request->only('nom', 'prenom', 'email', 'telephone');
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $donneesUser['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('photo')) {
            if ($stagiaire->user->photo) Storage::disk('public')->delete($stagiaire->user->photo);
            $donneesUser['photo'] = $request->file('photo')->store('photos', 'public');
        }
        $stagiaire->user->update($donneesUser);

        $donneesStagiaire = $request->only('ecole', 'specialite', 'niveau_etude', 'date_debut', 'date_fin', 'mentor_id', 'statut', 'description');
        if ($request->hasFile('cv')) {
            if ($stagiaire->cv) Storage::disk('public')->delete($stagiaire->cv);
            $donneesStagiaire['cv'] = $request->file('cv')->store('cvs', 'public');
        }
        $stagiaire->update($donneesStagiaire);

        return redirect()->route('admin.stagiaires.index')->with('succes', 'Stagiaire mis à jour.');
    }

    public function destroy(Stagiaire $stagiaire)
    {
        if ($stagiaire->cv) Storage::disk('public')->delete($stagiaire->cv);
        if ($stagiaire->user->photo) Storage::disk('public')->delete($stagiaire->user->photo);
        $stagiaire->user->delete();
        return redirect()->route('admin.stagiaires.index')->with('succes', 'Stagiaire supprimé.');
    }



}
