<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mesDocs = Document::where('user_id', $user->id)->latest()->get();
        $docsPartages = Document::where('partage_tous', true)
            ->where('user_id', '!=', $user->id)
            ->orWhereHas('partagesAvec', fn($q) => $q->where('user_id', $user->id))
            ->latest()->get();

        return view('documents.index', compact('mesDocs', 'docsPartages'));
    }

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->where('actif', true)->get();
        return view('documents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'fichier'     => 'required|file|max:51200',
            'partage_tous'=> 'boolean',
            'partages'    => 'nullable|array',
            'partages.*'  => 'exists:users,id',
        ]);

        $chemin = $request->file('fichier')->store('documents', 'public');
        $doc = Document::create([
            'user_id'      => Auth::id(),
            'titre'        => $request->titre,
            'description'  => $request->description,
            'fichier'      => $chemin,
            'type_fichier' => $request->file('fichier')->getClientOriginalExtension(),
            'taille'       => $request->file('fichier')->getSize(),
            'partage_tous' => $request->boolean('partage_tous'),
        ]);

        if (!$request->boolean('partage_tous') && $request->filled('partages')) {
            $doc->partagesAvec()->sync($request->partages);
        }

        return redirect()->route('documents.index')->with('succes', 'Document ajouté avec succès.');
    }

    /**
     * Affiche le détail du document avec un visualiseur intégré.
     * Pour les fichiers texte, lit le contenu et le passe à la vue.
     */
    public function show(Document $document)
    {
        $this->autoriser($document);
        $extension = strtolower($document->type_fichier);
        $texteExtensions = ['txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'log', 'md', 'sql'];
        $contenuTexte = null;
        if (in_array($extension, $texteExtensions)) {
            $chemin = storage_path('app/public/' . $document->fichier);
            if (file_exists($chemin)) {
                $contenuTexte = file_get_contents($chemin);
            }
        }
        return view('documents.show', compact('document', 'contenuTexte'));
    }

    public function edit(Document $document)
    {
        abort_if($document->user_id !== Auth::id(), 403);
        $users = User::where('id', '!=', Auth::id())->where('actif', true)->get();
        return view('documents.edit', compact('document', 'users'));
    }

    public function update(Request $request, Document $document)
    {
        abort_if($document->user_id !== Auth::id(), 403);

        $request->validate([
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'fichier'     => 'nullable|file|max:51200',
            'partage_tous'=> 'boolean',
            'partages'    => 'nullable|array',
        ]);

        $donnees = $request->only('titre', 'description', 'partage_tous');

        if ($request->hasFile('fichier')) {
            Storage::disk('public')->delete($document->fichier);
            $donnees['fichier']     = $request->file('fichier')->store('documents', 'public');
            $donnees['type_fichier']= $request->file('fichier')->getClientOriginalExtension();
            $donnees['taille']      = $request->file('fichier')->getSize();
        }

        $document->update($donnees);

        if (!$request->boolean('partage_tous')) {
            $document->partagesAvec()->sync($request->partages ?? []);
        }

        return redirect()->route('documents.index')->with('succes', 'Document mis à jour.');
    }

    public function destroy(Document $document)
    {
        abort_if($document->user_id !== Auth::id() && !Auth::user()->isAdmin(), 403);
        Storage::disk('public')->delete($document->fichier);
        $document->delete();
        return redirect()->route('documents.index')->with('succes', 'Document supprimé.');
    }

    /**
     * Télécharge le document (force le téléchargement).
     */
    public function telecharger(Document $document)
    {
        $this->autoriser($document);
        $chemin = storage_path('app/public/' . $document->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }

    /**
     * Diffuse le fichier dans le navigateur (affichage en ligne) pour le visualiseur.
     */
    public function stream(Document $document)
    {
        $this->autoriser($document);
        $chemin = storage_path('app/public/' . $document->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->file($chemin);
    }

    /**
     * Vérifie que l'utilisateur courant a le droit d'accéder au document.
     */
    private function autoriser(Document $document): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) return;
        abort_unless($document->estAccessiblePar($user), 403);
    }
}