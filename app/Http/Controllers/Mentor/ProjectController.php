<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Stagiaire;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projets = Project::with('stagiaires.user')
            ->where('mentor_id', Auth::id())->paginate(15);
        return view('mentor.projects.index', compact('projets'));
    }

    public function create()
    {
        $stagiaires = Stagiaire::with('user')->where('mentor_id', Auth::id())->where('statut', 'en_cours')->get();
        return view('mentor.projects.create', compact('stagiaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
            'statut'       => 'required|in:en_attente,en_cours,termine,suspendu',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'stagiaires'   => 'required|array|min:1',
            'stagiaires.*' => 'exists:stagiaires,id',
        ]);

        $projet = Project::create([
            'titre'       => $request->titre,
            'description' => $request->description,
            'date_debut'  => $request->date_debut,
            'date_fin'    => $request->date_fin,
            'statut'      => $request->statut,
            'priorite'    => $request->priorite,
            'mentor_id'   => Auth::id(),
        ]);

        $projet->stagiaires()->sync($request->stagiaires);

        return redirect()->route('mentor.projects.index')->with('succes', 'Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        abort_if($project->mentor_id !== Auth::id(), 403);
        $project->load('stagiaires.user', 'tasks', 'reports.stagiaire.user');
        return view('mentor.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        abort_if($project->mentor_id !== Auth::id(), 403);
        $stagiaires = Stagiaire::with('user')->where('mentor_id', Auth::id())->get();
        return view('mentor.projects.edit', compact('project', 'stagiaires'));
    }

    public function update(Request $request, Project $project)
    {
        abort_if($project->mentor_id !== Auth::id(), 403);
        $request->validate([
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
            'statut'       => 'required|in:en_attente,en_cours,termine,suspendu',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'stagiaires'   => 'required|array|min:1',
            'stagiaires.*' => 'exists:stagiaires,id',
        ]);

        $project->update($request->only('titre', 'description', 'date_debut', 'date_fin', 'statut', 'priorite'));
        $project->stagiaires()->sync($request->stagiaires);

        return redirect()->route('mentor.projects.index')->with('succes', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        abort_if($project->mentor_id !== Auth::id(), 403);
        $project->delete();
        return redirect()->route('mentor.projects.index')->with('succes', 'Projet supprimé.');
    }
}