<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Mentor;
use App\Models\Report;
use App\Models\Presence;
use App\Models\Project;
use App\Models\Attestation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'      => User::count(),
            'total_stagiaires' => Stagiaire::count(),
            'total_mentors'    => Mentor::count(),
            'stagiaires_actifs'=> Stagiaire::where('statut', 'en_cours')->count(),
            'total_projets'    => Project::count(),
            'rapports_en_attente' => Report::where('statut', 'soumis')->count(),
            'attestations_en_attente' => Attestation::where('statut', 'valide_mentor')->count(),
            'presences_aujourd_hui' => Presence::whereDate('date', today())->count(),
        ];

        $stagiairesRecents = Stagiaire::with('user', 'mentor')
            ->latest()->limit(5)->get();

        $rapportsRecents = Report::with('stagiaire.user')
            ->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'stagiairesRecents', 'rapportsRecents'));
    }
}