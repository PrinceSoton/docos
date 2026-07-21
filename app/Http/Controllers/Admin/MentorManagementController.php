<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\User;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Mail\BienvenueMentor;
use Illuminate\Support\Facades\Mail;

class MentorManagementController extends Controller
{
    public function index()
    {
        $mentors = Mentor::with('user', 'stagiaires.user')->paginate(15);
        return view('admin.mentors.index', compact('mentors'));
    }

    public function create()
    {
        return view('admin.mentors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'email'       => 'required|email|unique:users',
            'telephone'   => 'nullable|string|max:20',
            'password'    => 'required|min:8|confirmed',
            'departement' => 'nullable|string|max:100',
            'poste'       => 'nullable|string|max:100',
            'bio'         => 'nullable|string',
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
            'role'     => 'mentor',
            'actif'    => true,
            'photo'    => $photoPath,
        ]);

        Mentor::create([
            'user_id'     => $user->id,
            'departement' => $request->departement,
            'poste'       => $request->poste,
            'bio'         => $request->bio,
        ]);


        Mail::to($request->email)->send(new BienvenueMentor($request->only('nom', 'prenom', 'email'), $request->password));


        return redirect()->route('admin.mentors.index')->with('succes', 'Mentor créé avec succès.');
    }

    public function edit(Mentor $mentor)
    {
        return view('admin.mentors.edit', compact('mentor'));
    }

    public function update(Request $request, Mentor $mentor)
    {
        $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $mentor->user_id,
            'departement' => 'nullable|string|max:100',
            'poste'       => 'nullable|string|max:100',
            'bio'         => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $donnees = $request->only('nom', 'prenom', 'email', 'telephone');
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $donnees['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('photo')) {
            if ($mentor->user->photo) Storage::disk('public')->delete($mentor->user->photo);
            $donnees['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $mentor->user->update($donnees);
        $mentor->update($request->only('departement', 'poste', 'bio'));

        return redirect()->route('admin.mentors.index')->with('succes', 'Mentor mis à jour.');
    }

    public function destroy(Mentor $mentor)
    {
        Stagiaire::where('mentor_id', $mentor->user_id)->update(['mentor_id' => null]);
        if ($mentor->user->photo) Storage::disk('public')->delete($mentor->user->photo);
        $mentor->user->delete();
        return redirect()->route('admin.mentors.index')->with('succes', 'Mentor supprimé.');
    }

    public function assign(Request $request)
    {
        $mentors    = Mentor::with('user')->get();
        $stagiaires = Stagiaire::with('user')->where('statut', 'en_cours')->get();
        return view('admin.mentors.assign', compact('mentors', 'stagiaires'));
    }

    public function doAssign(Request $request)
    {
        $request->validate([
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'mentor_id'    => 'required|exists:users,id',
        ]);

        Stagiaire::findOrFail($request->stagiaire_id)
            ->update(['mentor_id' => $request->mentor_id]);

        return redirect()->route('admin.mentors.index')->with('succes', 'Stagiaire affecté au mentor.');
    }

    public function removeAssign(Stagiaire $stagiaire)
    {
        $stagiaire->update(['mentor_id' => null]);
        return back()->with('succes', 'Affectation supprimée.');
    }
}
