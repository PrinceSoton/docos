<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvenementManagementController extends Controller
{
    public function index()
    {
        $evenements = Evenement::with('creePar')->latest()->paginate(15);
        return view('admin.evenements.index', compact('evenements'));
    }

    public function create()
    {
        $users = User::where('actif', true)->get();
        return view('admin.evenements.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'          => 'required|string|max:200',
            'contenu'        => 'nullable|string',
            'type'           => 'required|in:information,evenement,note',
            'date_evenement' => 'nullable|date',
            'partage_tous'   => 'boolean',
            'destinataires'  => 'nullable|array',
            'destinataires.*'=> 'exists:users,id',
            'image'          => 'nullable|image|max:5120',
            'fichier'        => 'nullable|file|max:20480',
        ]);

        $donnees = $request->only('titre', 'contenu', 'type', 'date_evenement', 'partage_tous');
        $donnees['cree_par'] = Auth::id();

        if ($request->hasFile('image')) {
            $donnees['image'] = $request->file('image')->store('evenements/images', 'public');
        }
        if ($request->hasFile('fichier')) {
            $donnees['fichier'] = $request->file('fichier')->store('evenements/fichiers', 'public');
        }

        $evenement = Evenement::create($donnees);

        if (!$request->boolean('partage_tous') && $request->filled('destinataires')) {
            $evenement->utilisateurssCibles()->sync($request->destinataires);
        }

        return redirect()->route('admin.evenements.index')->with('succes', 'Évènement créé avec succès.');
    }

    public function show(Evenement $evenement)
    {
        $evenement->load('creePar', 'utilisateurssCibles');
        return view('admin.evenements.show', compact('evenement'));
    }

    public function edit(Evenement $evenement)
    {
        $users = User::where('actif', true)->get();
        return view('admin.evenements.edit', compact('evenement', 'users'));
    }

    public function update(Request $request, Evenement $evenement)
    {
        $request->validate([
            'titre'          => 'required|string|max:200',
            'contenu'        => 'nullable|string',
            'type'           => 'required|in:information,evenement,note',
            'date_evenement' => 'nullable|date',
            'partage_tous'   => 'boolean',
            'destinataires'  => 'nullable|array',
            'image'          => 'nullable|image|max:5120',
            'fichier'        => 'nullable|file|max:20480',
        ]);

        $donnees = $request->only('titre', 'contenu', 'type', 'date_evenement', 'partage_tous');

        if ($request->hasFile('image')) {
            if ($evenement->image) Storage::disk('public')->delete($evenement->image);
            $donnees['image'] = $request->file('image')->store('evenements/images', 'public');
        }
        if ($request->hasFile('fichier')) {
            if ($evenement->fichier) Storage::disk('public')->delete($evenement->fichier);
            $donnees['fichier'] = $request->file('fichier')->store('evenements/fichiers', 'public');
        }

        $evenement->update($donnees);

        if (!$request->boolean('partage_tous')) {
            $evenement->utilisateurssCibles()->sync($request->destinataires ?? []);
        }

        return redirect()->route('admin.evenements.index')->with('succes', 'Évènement mis à jour.');
    }

    public function destroy(Evenement $evenement)
    {
        if ($evenement->image) Storage::disk('public')->delete($evenement->image);
        if ($evenement->fichier) Storage::disk('public')->delete($evenement->fichier);
        $evenement->delete();
        return redirect()->route('admin.evenements.index')->with('succes', 'Évènement supprimé.');
    }
}