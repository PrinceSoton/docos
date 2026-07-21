<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $stagiaire = Auth::user()->stagiaire;
        $aFaire    = Task::with('project')->where('stagiaire_id', $stagiaire->id)->where('statut', 'a_faire')->orderBy('priorite')->get();
        $enCours   = Task::with('project')->where('stagiaire_id', $stagiaire->id)->where('statut', 'en_cours')->orderBy('priorite')->get();
        $terminees = Task::with('project')->where('stagiaire_id', $stagiaire->id)->where('statut', 'termine')->latest()->get();
        return view('stagiaire.tasks.index', compact('aFaire', 'enCours', 'terminees'));
    }

    public function show(Task $task)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($task->stagiaire_id !== $stagiaire->id, 403);
        $task->load('project');
        return view('stagiaire.tasks.show', compact('task'));
    }

    public function updateStatut(Request $request, Task $task)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($task->stagiaire_id !== $stagiaire->id, 403);
        $request->validate(['statut' => 'required|in:a_faire,en_cours,termine']);
        $task->update(['statut' => $request->statut]);
        return back()->with('succes', 'Statut mis à jour.');
    }
}