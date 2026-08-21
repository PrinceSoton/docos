<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Presence;
use App\Models\Permission;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $stagiaires = Stagiaire::with('user')->where('statut', 'en_cours')->get();
        $stagiaire  = null;
        $query = Presence::with('stagiaire.user');

        if ($request->filled('stagiaire_id')) {
            $stagiaire = Stagiaire::with('user')->findOrFail($request->stagiaire_id);
            $query->where('stagiaire_id', $stagiaire->id);
        }

        if ($request->filled('mois')) {
            $query->whereMonth('date', date('m', strtotime($request->mois)))
                  ->whereYear('date', date('Y', strtotime($request->mois)));
        }

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