<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $stagiaire = Auth::user()->stagiaire;
        $projets   = $stagiaire->projects()->with('mentor')->paginate(10);
        return view('stagiaire.projects.index', compact('projets'));
    }

    public function show(Project $project)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_unless($project->stagiaires()->where('stagiaire_id', $stagiaire->id)->exists(), 403);
        $project->load('mentor', 'tasks', 'reports');
        $taches = [
            'a_faire'  => $project->tasks()->where('stagiaire_id', $stagiaire->id)->where('statut', 'a_faire')->get(),
            'en_cours' => $project->tasks()->where('stagiaire_id', $stagiaire->id)->where('statut', 'en_cours')->get(),
            'termine'  => $project->tasks()->where('stagiaire_id', $stagiaire->id)->where('statut', 'termine')->get(),
        ];
        return view('stagiaire.projects.show', compact('project', 'taches'));
    }
}