<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Presence;
use App\Models\Report;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class StagiaireController extends Controller
{
    public function index()
    {
        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', Auth::id())->paginate(15);
        return view('mentor.stagiaires.index', compact('stagiaires'));
    }

    public function show(Stagiaire $stagiaire)
    {
        abort_if($stagiaire->mentor_id !== Auth::id(), 403);

        $stagiaire->load('user', 'projects', 'tasks', 'reports', 'presences');

        $stats = [
            'present' => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'present')->count(),
            'retard'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'retard')->count(),
            'absent'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'absent')->count(),
            'taches_terminees' => Task::where('stagiaire_id', $stagiaire->id)->where('statut', 'termine')->count(),
            'taches_total'     => Task::where('stagiaire_id', $stagiaire->id)->count(),
            'rapports'         => Report::where('stagiaire_id', $stagiaire->id)->count(),
        ];

        $progression = $stats['taches_total'] > 0
            ? round(($stats['taches_terminees'] / $stats['taches_total']) * 100)
            : 0;

        return view('mentor.stagiaires.show', compact('stagiaire', 'stats', 'progression'));
    }
}