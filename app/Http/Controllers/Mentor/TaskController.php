<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $projets = Project::with('tasks.stagiaire.user')
            ->where(function ($q) {
                $q->where('mentor_id', Auth::id())
                  ->orWhereHas('mentors', fn($q2) => $q2->where('user_id', Auth::id()));
            })
            ->get();

        return view('mentor.tasks.index', compact('projets'));
    }

    public function create()
    {
        $projets = Project::where(function ($q) {
            $q->where('mentor_id', Auth::id())
              ->orWhereHas('mentors', fn($q2) => $q2->where('user_id', Auth::id()));
        })->get();

        $stagiaires = collect();

        if (old('project_id')) {
            $project = Project::find(old('project_id'));
            if ($project && $project->hasAccess(Auth::user())) {
                $stagiaires = $project->stagiaires()->with('user')->get();
            }
        }

        return view('mentor.tasks.create', compact('projets', 'stagiaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'statut'       => 'required|in:a_faire,en_cours,termine',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'difficulte'   => 'required|in:facile,moyen,difficile',
            'date_echeance'=> 'nullable|date',
        ]);

        $projet = Project::findOrFail($request->project_id);
        abort_unless($projet->hasAccess(Auth::user()), 403);

        // Vérifier que le stagiaire appartient au projet
        if (!$projet->stagiaires()->where('stagiaire_id', $request->stagiaire_id)->exists()) {
            return back()->withErrors(['stagiaire_id' => 'Ce stagiaire n\'est pas associé à ce projet.'])->withInput();
        }

        Task::create($request->only('project_id', 'stagiaire_id', 'titre', 'description', 'statut', 'priorite', 'difficulte', 'date_echeance'));

        return redirect()->route('mentor.tasks.index')->with('succes', 'Tâche créée avec succès.');
    }

    public function edit(Task $task)
    {
        abort_unless($task->project->hasAccess(Auth::user()), 403);

        $projets = Project::where(function ($q) {
            $q->where('mentor_id', Auth::id())
              ->orWhereHas('mentors', fn($q2) => $q2->where('user_id', Auth::id()));
        })->get();

        $stagiaires = $task->project->stagiaires()->with('user')->get();

        return view('mentor.tasks.edit', compact('task', 'projets', 'stagiaires'));
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($task->project->hasAccess(Auth::user()), 403);

        $request->validate([
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'statut'       => 'required|in:a_faire,en_cours,termine',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'difficulte'   => 'required|in:facile,moyen,difficile',
            'date_echeance'=> 'nullable|date',
        ]);

        $task->update($request->only('titre', 'description', 'statut', 'priorite', 'difficulte', 'date_echeance'));

        return redirect()->route('mentor.tasks.index')->with('succes', 'Tâche mise à jour.');
    }

    public function destroy(Task $task)
    {
        abort_unless($task->project->hasAccess(Auth::user()), 403);
        $task->delete();
        return back()->with('succes', 'Tâche supprimée.');
    }

    /** API : récupérer les stagiaires d'un projet (accessible à tous les mentors du projet) */
    public function getStagiairesByProject(Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);

        $stagiaires = $project->stagiaires()->with('user')->get();

        return response()->json($stagiaires->map(fn($s) => [
            'id'        => $s->id,
            'nom'       => $s->user->nom_complet,
            'matricule' => $s->matricule,
        ]));
    }
}