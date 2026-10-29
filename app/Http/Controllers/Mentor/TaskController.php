<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    
    /*  Helpers privés                                                     */
    

    /** Vérifie qu'un mentor a accès à une tâche (projet OU indépendante) */
    private function hasAccessToTask(Task $task): bool
    {
        if ($task->project_id) {
            return $task->project->hasAccess(Auth::user());
        }
        return $task->stagiaire && $task->stagiaire->mentor_id === Auth::id();
    }

    /** Projets accessibles au mentor (créateur OU collaborateur) */
    private function projetsAccessibles()
    {
        return Project::where(function ($q) {
            $q->where('mentor_id', Auth::id())
              ->orWhereHas('mentors', fn($q2) => $q2->where('user_id', Auth::id()));
        })->get();
    }

    
    /*  Liste : tâches indépendantes + tâches par projet                   */
    

    public function index()
    {
        // Tâches groupées par projet
        $projets = Project::with('tasks.stagiaire.user')
            ->where(function ($q) {
                $q->where('mentor_id', Auth::id())
                  ->orWhereHas('mentors', fn($q2) => $q2->where('user_id', Auth::id()));
            })
            ->get();

        // Tâches indépendantes des stagiaires de ce mentor uniquement
        $tachesIndependantes = Task::with('stagiaire.user')
            ->whereNull('project_id')
            ->whereHas('stagiaire', fn($q) => $q->where('mentor_id', Auth::id()))
            ->orderByDesc('created_at')
            ->get();

        return view('mentor.tasks.index', compact('projets', 'tachesIndependantes'));
    }

    
    /*  Création                                                          */
    

    public function create()
    {
        $projets = $this->projetsAccessibles();

        // Stagiaires par défaut (mentor's own) quand aucun projet n'est présélectionné
        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', Auth::id())
            ->where('statut', 'en_cours')
            ->orderBy('matricule')
            ->get();

        // Pré-remplir les stagiaires si un projet est déjà sélectionné (ancien input)
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
            'project_id'   => 'nullable|exists:projects,id',
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'statut'       => 'required|in:a_faire,en_cours,termine',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'difficulte'   => 'required|in:facile,moyen,difficile',
            'date_echeance'=> 'nullable|date',
        ]);

        $stagiaire = Stagiaire::findOrFail($request->stagiaire_id);

        // ---------- Cas 1 : tâche liée à un projet ----------
        if ($request->filled('project_id')) {
            $projet = Project::findOrFail($request->project_id);
            abort_unless($projet->hasAccess(Auth::user()), 403);

            // Le stagiaire doit être rattaché au projet
            if (!$projet->stagiaires()->where('stagiaire_id', $stagiaire->id)->exists()) {
                return back()
                    ->withErrors(['stagiaire_id' => 'Ce stagiaire n\'est pas associé à ce projet.'])
                    ->withInput();
            }
        }
        // ---------- Cas 2 : tâche indépendante ----------
        else {
            // Le stagiaire doit appartenir au mentor connecté
            abort_unless(
                $stagiaire->mentor_id === Auth::id(),
                403,
                'Vous ne pouvez assigner une tâche indépendante qu\'à vos propres stagiaires.'
            );
        }

        Task::create([
            'project_id'   => $request->filled('project_id') ? $request->project_id : null,
            'stagiaire_id' => $stagiaire->id,
            'titre'        => $request->titre,
            'description'  => $request->description,
            'statut'       => $request->statut,
            'priorite'     => $request->priorite,
            'difficulte'   => $request->difficulte,
            'date_echeance'=> $request->date_echeance,
        ]);

        return redirect()->route('mentor.tasks.index')
            ->with('succes', 'Tâche créée avec succès.');
    }

    
    /*  Édition                                                           */
    

    public function edit(Task $task)
    {
        abort_unless($this->hasAccessToTask($task), 403);

        $projets = $this->projetsAccessibles();

        // Stagiaires disponibles selon le contexte
        $projectId = old('project_id', $task->project_id);
        if ($projectId) {
            $project = Project::find($projectId);
            $stagiaires = $project && $project->hasAccess(Auth::user())
                ? $project->stagiaires()->with('user')->get()
                : collect();
        } else {
            $stagiaires = Stagiaire::with('user')
                ->where('mentor_id', Auth::id())
                ->where('statut', 'en_cours')
                ->orderBy('matricule')
                ->get();
        }

        return view('mentor.tasks.edit', compact('task', 'projets', 'stagiaires'));
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($this->hasAccessToTask($task), 403);

        $request->validate([
            'project_id'   => 'nullable|exists:projects,id',
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'statut'       => 'required|in:a_faire,en_cours,termine',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'difficulte'   => 'required|in:facile,moyen,difficile',
            'date_echeance'=> 'nullable|date',
        ]);

        $stagiaire = Stagiaire::findOrFail($request->stagiaire_id);

        // Vérification d'accès selon le contexte final
        if ($request->filled('project_id')) {
            $projet = Project::findOrFail($request->project_id);
            abort_unless($projet->hasAccess(Auth::user()), 403);

            if (!$projet->stagiaires()->where('stagiaire_id', $stagiaire->id)->exists()) {
                return back()
                    ->withErrors(['stagiaire_id' => 'Ce stagiaire n\'est pas associé à ce projet.'])
                    ->withInput();
            }

            $projectId = $projet->id;
        } else {
            abort_unless(
                $stagiaire->mentor_id === Auth::id(),
                403,
                'Vous ne pouvez assigner une tâche indépendante qu\'à vos propres stagiaires.'
            );
            $projectId = null;
        }

        $task->update([
            'project_id'   => $projectId,
            'stagiaire_id' => $stagiaire->id,
            'titre'        => $request->titre,
            'description'  => $request->description,
            'statut'       => $request->statut,
            'priorite'     => $request->priorite,
            'difficulte'   => $request->difficulte,
            'date_echeance'=> $request->date_echeance,
        ]);

        return redirect()->route('mentor.tasks.index')
            ->with('succes', 'Tâche mise à jour.');
    }

    
    /*  Suppression                                                       */
    

    public function destroy(Task $task)
    {
        abort_unless($this->hasAccessToTask($task), 403);
        $task->delete();
        return back()->with('succes', 'Tâche supprimée.');
    }

    
    /*  API : stagiaires disponibles (avec ou sans projet)                */
    

    /**
     * Retourne les stagiaires à afficher dans le select du formulaire de tâche.
     *  - ?project_id=X  → stagiaires du projet (accessible au mentor)
     *  - (aucun)        → stagiaires du mentor connecté (tâche indépendante)
     */
    public function getStagiairesDisponibles(Request $request)
    {
        if ($request->filled('project_id')) {
            $project = Project::findOrFail($request->project_id);
            abort_unless($project->hasAccess(Auth::user()), 403);

            $stagiaires = $project->stagiaires()->with('user')->get();
        } else {
            $stagiaires = Stagiaire::with('user')
                ->where('mentor_id', Auth::id())
                ->where('statut', 'en_cours')
                ->orderBy('matricule')
                ->get();
        }

        return response()->json($stagiaires->map(fn($s) => [
            'id'        => $s->id,
            'nom'       => $s->user->nom_complet,
            'matricule' => $s->matricule,
        ]));
    }

    
    /*  Ancien endpoint (conservé pour compatibilité)                      */
    

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
