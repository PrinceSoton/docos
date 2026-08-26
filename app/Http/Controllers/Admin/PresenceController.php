<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Presence;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $stagiaires = Stagiaire::with('user')->where('statut', 'en_cours')->get();
        $stagiaire  = null;
        $query = Presence::with('stagiaire.user');

        // Filtre par stagiaire
        if ($request->filled('stagiaire_id')) {
            $stagiaire = Stagiaire::with('user')->findOrFail($request->stagiaire_id);
            $query->where('stagiaire_id', $stagiaire->id);
        }

        // --- Filtres temporels (priorité : date > mois > année) ---
        if ($request->filled('date')) {
            // Filtre par jour précis
            $date = Carbon::parse($request->date);
            $query->whereDate('date', $date);
        } elseif ($request->filled('mois')) {
            // Filtre par mois (année + mois)
            $mois = Carbon::parse($request->mois . '-01');
            $query->whereYear('date', $mois->year)
                  ->whereMonth('date', $mois->month);
        } elseif ($request->filled('annee')) {
            // Filtre par année
            $annee = (int) $request->annee;
            $query->whereYear('date', $annee);
        }

        // Pagination avec conservation des paramètres de requête
        $presences = $query->orderBy('date', 'desc')->paginate(30)->withQueryString();

        return view('admin.presences.index', compact('stagiaires', 'stagiaire', 'presences'));
    }

    public function show(Stagiaire $stagiaire)
    {
        $presences  = Presence::where('stagiaire_id', $stagiaire->id)
            ->orderBy('date', 'desc')->paginate(30);
        $permissions = Permission::where('stagiaire_id', $stagiaire->id)
            ->orderBy('date_debut', 'desc')->get();
        $stats = [
            'present' => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'present')->count(),
            'retard'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'retard')->count(),
            'absent'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'absent')->count(),
        ];
        return view('admin.presences.show', compact('stagiaire', 'presences', 'permissions', 'stats'));
    }
}
