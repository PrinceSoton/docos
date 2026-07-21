<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Task;
use App\Models\Report;
use App\Models\Permission;
use App\Models\Attestation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if(!$stagiaire, 404, 'Profil stagiaire introuvable.');

        $stats = [
            'present'    => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'present')->count(),
            'retard'     => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'retard')->count(),
            'absent'     => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'absent')->count(),
            'a_faire'    => Task::where('stagiaire_id', $stagiaire->id)->where('statut', 'a_faire')->count(),
            'en_cours'   => Task::where('stagiaire_id', $stagiaire->id)->where('statut', 'en_cours')->count(),
            'terminees'  => Task::where('stagiaire_id', $stagiaire->id)->where('statut', 'termine')->count(),
            'rapports'   => Report::where('stagiaire_id', $stagiaire->id)->count(),
            'permissions'=> Permission::where('stagiaire_id', $stagiaire->id)->where('statut', 'en_attente')->count(),
        ];

        $progression = ($stats['a_faire'] + $stats['en_cours'] + $stats['terminees']) > 0
            ? round(($stats['terminees'] / ($stats['a_faire'] + $stats['en_cours'] + $stats['terminees'])) * 100)
            : 0;

        $tachesRecentes = Task::where('stagiaire_id', $stagiaire->id)->latest()->limit(5)->get();
        $rapportsRecents = Report::where('stagiaire_id', $stagiaire->id)->latest()->limit(5)->get();

        // Durée de stage
        $debut = $stagiaire->date_debut;
        $fin   = $stagiaire->date_fin;
        $joursRestants = now()->lessThan($fin) ? (int) now()->diffInDays($fin) : 0;

        return view('stagiaire.dashboard', compact('stagiaire', 'stats', 'progression', 'tachesRecentes', 'rapportsRecents', 'joursRestants'));
    }
}