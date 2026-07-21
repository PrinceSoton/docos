<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\Attestation;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $mentorId   = Auth::id();
        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', $mentorId)
            ->where('statut', 'en_cours')->get();

        $stats = [
            'stagiaires'       => $stagiaires->count(),
            'projets'          => Project::where('mentor_id', $mentorId)->count(),
            'rapports_soumis'  => Report::whereIn('stagiaire_id', $stagiaires->pluck('id'))->where('statut', 'soumis')->count(),
            'permissions_en_attente' => Permission::whereIn('stagiaire_id', $stagiaires->pluck('id'))->where('statut', 'en_attente')->count(),
            'attestations_a_valider' => Attestation::whereIn('stagiaire_id', $stagiaires->pluck('id'))->where('statut', 'en_attente')->count(),
        ];

        $avancementStagiaires = $stagiaires->map(function ($stag) {
            $totalTaches    = $stag->tasks()->count();
            $tachesTerminees = $stag->tasks()->where('statut', 'termine')->count();
            $stag->progression = $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : 0;
            return $stag;
        });

        $rapportsRecents = Report::with('stagiaire.user')
            ->whereIn('stagiaire_id', $stagiaires->pluck('id'))
            ->where('statut', 'soumis')->latest()->limit(5)->get();

        $permissionsEnAttente = Permission::with('stagiaire.user')
            ->whereIn('stagiaire_id', $stagiaires->pluck('id'))
            ->where('statut', 'en_attente')->get();

        return view('mentor.dashboard', compact('stats', 'avancementStagiaires', 'rapportsRecents', 'permissionsEnAttente'));
    }
}