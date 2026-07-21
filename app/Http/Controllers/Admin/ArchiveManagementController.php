<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveFichier;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArchiveManagementController extends Controller
{
    public function index()
    {
        $archives = Archive::with('stagiaire.user', 'creePar', 'fichiers')
            ->latest()->paginate(15);
        return view('admin.archives.index', compact('archives'));
    }

    public function create()
    {
        $stagiaires = Stagiaire::with('user')->get();
        return view('admin.archives.create', compact('stagiaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'         => 'required|in:stagiaire,autre',
            'stagiaire_id' => 'nullable|required_if:type,stagiaire|exists:stagiaires,id',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'fichiers'     => 'nullable|array',
            'fichiers.*'   => 'file|max:51200',
        ]);

        $archive = Archive::create([
            'type'         => $request->type,
            'stagiaire_id' => $request->type === 'stagiaire' ? $request->stagiaire_id : null,
            'titre'        => $request->titre,
            'description'  => $request->description,
            'cree_par'     => Auth::id(),
        ]);

        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $fichier) {
                $chemin = $fichier->store('archives', 'public');
                ArchiveFichier::create([
                    'archive_id'   => $archive->id,
                    'nom_original' => $fichier->getClientOriginalName(),
                    'chemin'       => $chemin,
                    'type_fichier' => $fichier->getClientOriginalExtension(),
                    'taille'       => $fichier->getSize(),
                ]);
            }
        }

        return redirect()->route('admin.archives.index')->with('succes', 'Archive créée avec succès.');
    }

    public function show(Archive $archive)
    {
        $archive->load('stagiaire.user', 'fichiers', 'creePar');
        return view('admin.archives.show', compact('archive'));
    }

    public function edit(Archive $archive)
    {
        $stagiaires = Stagiaire::with('user')->get();
        $archive->load('fichiers');
        return view('admin.archives.edit', compact('archive', 'stagiaires'));
    }

    public function update(Request $request, Archive $archive)
    {
        $request->validate([
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'fichiers'     => 'nullable|array',
            'fichiers.*'   => 'file|max:51200',
        ]);

        $archive->update($request->only('titre', 'description'));

        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $fichier) {
                $chemin = $fichier->store('archives', 'public');
                ArchiveFichier::create([
                    'archive_id'   => $archive->id,
                    'nom_original' => $fichier->getClientOriginalName(),
                    'chemin'       => $chemin,
                    'type_fichier' => $fichier->getClientOriginalExtension(),
                    'taille'       => $fichier->getSize(),
                ]);
            }
        }

        return redirect()->route('admin.archives.show', $archive)->with('succes', 'Archive mise à jour.');
    }

    public function destroy(Archive $archive)
    {
        foreach ($archive->fichiers as $f) {
            Storage::disk('public')->delete($f->chemin);
        }
        $archive->delete();
        return redirect()->route('admin.archives.index')->with('succes', 'Archive supprimée.');
    }

    public function supprimerFichier(ArchiveFichier $fichier)
    {
        Storage::disk('public')->delete($fichier->chemin);
        $archiveId = $fichier->archive_id;
        $fichier->delete();
        return redirect()->route('admin.archives.show', $archiveId)->with('succes', 'Fichier supprimé.');
    }

    public function telecharger(ArchiveFichier $fichier)
    {
        $chemin = storage_path('app/public/' . $fichier->chemin);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin, $fichier->nom_original);
    }
}