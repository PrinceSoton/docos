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
            ->where('mentor_id', Auth::id())->get();
        return view('mentor.tasks.index', compact('projets'));
    }

    public function create()
    {
        $projets    = Project::where('mentor_id', Auth::id())->get();
        $stagiaires = Stagiaire::with('user')->where('mentor_id', Auth::id())->get();
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
        abort_if($projet->mentor_id !== Auth::id(), 403);

        Task::create($request->only('project_id', 'stagiaire_id', 'titre', 'description', 'statut', 'priorite', 'difficulte', 'date_echeance'));

        return redirect()->route('mentor.tasks.index')->with('succes', 'Tâche créée avec succès.');
    }

    public function edit(Task $task)
    {
        abort_if($task->project->mentor_id !== Auth::id(), 403);
        $projets    = Project::where('mentor_id', Auth::id())->get();
        $stagiaires = Stagiaire::with('user')->where('mentor_id', Auth::id())->get();
        return view('mentor.tasks.edit', compact('task', 'projets', 'stagiaires'));
    }

    public function update(Request $request, Task $task)
    {
        abort_if($task->project->mentor_id !== Auth::id(), 403);
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
        abort_if($task->project->mentor_id !== Auth::id(), 403);
        $task->delete();
        return back()->with('succes', 'Tâche supprimée.');
    }
}