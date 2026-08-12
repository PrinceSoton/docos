<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $stagiaire = Auth::user()->stagiaire;
        $rapports  = Report::with('project')->where('stagiaire_id', $stagiaire->id)->latest()->paginate(15);
        return view('stagiaire.reports.index', compact('rapports'));
    }

    public function create()
    {
        $stagiaire = Auth::user()->stagiaire;
        $projets   = $stagiaire->projects()->get();
        return view('stagiaire.reports.create', compact('projets'));
    }

    public function store(Request $request)
    {
         $rules = [
        'titre'      => 'required|string|max:200',
        'description'=> 'nullable|string',
        'type'       => 'required|in:journalier,hebdomadaire,mensuel,final,autre',
        'project_id' => 'nullable|exists:projects,id',
        'fichier'    => 'required|file|max:20480',
    ];
    $request->validate($rules);

    // Validation conditionnelle pour type_autre
    $data = $request->only('titre', 'description', 'type', 'project_id');
    if ($request->type === 'autre') {
        $request->validate(['type_autre' => 'required|string|max:255']);
        $data['type_autre'] = $request->type_autre;
    } else {
        $data['type_autre'] = null;
    }

    $stagiaire = Auth::user()->stagiaire;
    $chemin = $request->file('fichier')->store('rapports', 'public');

    Report::create(array_merge($data, [
        'stagiaire_id' => $stagiaire->id,
        'fichier'      => $chemin,
        'statut'       => 'soumis',
    ]));

        return redirect()->route('stagiaire.reports.index')->with('succes', 'Rapport déposé avec succès.');
    }

    public function show(Report $report)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($report->stagiaire_id !== $stagiaire->id, 403);
        $report->load('project', 'comments.user', 'validePar');
        return view('stagiaire.reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($report->stagiaire_id !== $stagiaire->id, 403);
        abort_if($report->statut !== 'soumis', 403, 'Rapport non modifiable.');
        $projets = $stagiaire->projects()->get();
        return view('stagiaire.reports.edit', compact('report', 'projets'));
    }

    public function update(Request $request, Report $report)
{
    $stagiaire = Auth::user()->stagiaire;
    abort_if($report->stagiaire_id !== $stagiaire->id, 403);
    abort_if($report->statut !== 'soumis', 403, 'Rapport non modifiable.');

    $rules = [
        'titre'      => 'required|string|max:200',
        'description'=> 'nullable|string',
        'type'       => 'required|in:journalier,hebdomadaire,mensuel,final,autre',
        'project_id' => 'nullable|exists:projects,id',
        'fichier'    => 'nullable|file|max:20480',
    ];
    $request->validate($rules);

    $data = $request->only('titre', 'description', 'type', 'project_id');
    if ($request->type === 'autre') {
        $request->validate(['type_autre' => 'required|string|max:255']);
        $data['type_autre'] = $request->type_autre;
    } else {
        $data['type_autre'] = null;
    }

    if ($request->hasFile('fichier')) {
        Storage::disk('public')->delete($report->fichier);
        $data['fichier'] = $request->file('fichier')->store('rapports', 'public');
    }

    $report->update($data);
    return redirect()->route('stagiaire.reports.index')->with('succes', 'Rapport mis à jour.');
}

    public function destroy(Report $report)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($report->stagiaire_id !== $stagiaire->id, 403);
        abort_if($report->statut !== 'soumis', 403, 'Rapport non supprimable.');
        Storage::disk('public')->delete($report->fichier);
        $report->delete();
        return redirect()->route('stagiaire.reports.index')->with('succes', 'Rapport supprimé.');
    }

    public function telecharger(Report $report)
    {
        $stagiaire = Auth::user()->stagiaire;
        $user      = Auth::user();
        if (!$user->isAdmin() && !$user->isMentor()) {
            abort_if($report->stagiaire_id !== $stagiaire->id, 403);
        }
        $chemin = storage_path('app/public/' . $report->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }
}
