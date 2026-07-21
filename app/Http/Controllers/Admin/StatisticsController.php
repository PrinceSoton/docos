<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Mentor;
use App\Models\Presence;
use App\Models\Report;
use App\Models\Project;
use App\Models\Task;
use App\Models\Attestation;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        $stats = [
            'users'            => User::count(),
            'stagiaires'       => Stagiaire::count(),
            'stagiaires_actifs'=> Stagiaire::where('statut', 'en_cours')->count(),
            'stagiaires_fin'   => Stagiaire::where('statut', 'termine')->count(),
            'mentors'          => Mentor::count(),
            'projets'          => Project::count(),
            'projets_en_cours' => Project::where('statut', 'en_cours')->count(),
            'taches'           => Task::count(),
            'taches_terminees' => Task::where('statut', 'termine')->count(),
            'rapports'         => Report::count(),
            'rapports_valides' => Report::where('statut', 'valide')->count(),
            'presences_total'  => Presence::count(),
            'absences'         => Presence::where('statut', 'absent')->count(),
            'retards'          => Presence::where('statut', 'retard')->count(),
            'attestations'     => Attestation::count(),
            'attestations_envoyees' => Attestation::where('statut', 'envoye')->count(),
        ];

        // Présences par mois (6 derniers mois)
        $presencesParMois = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $presencesParMois[] = [
                'mois'    => $date->translatedFormat('M Y'),
                'present' => Presence::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('statut', 'present')->count(),
                'retard'  => Presence::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('statut', 'retard')->count(),
                'absent'  => Presence::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('statut', 'absent')->count(),
            ];
        }

        // Stagiaires par mentor
        $stagiaresParMentor = Mentor::with('user')
            ->withCount('stagiaires')->get();

        return view('admin.statistics.index', compact('stats', 'presencesParMois', 'stagiaresParMentor'));
    }
}