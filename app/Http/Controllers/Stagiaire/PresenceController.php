<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Permission;
use App\Models\Calendar;
use App\Models\ConfigJoursTravail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresenceController extends Controller
{
    public function index()
    {
        $stagiaire   = Auth::user()->stagiaire;
        $presences   = Presence::where('stagiaire_id', $stagiaire->id)->orderBy('date', 'desc')->paginate(30);
        $permissions = Permission::where('stagiaire_id', $stagiaire->id)->orderBy('date_debut', 'desc')->get();
        $presenceAujourdhui = Presence::where('stagiaire_id', $stagiaire->id)->whereDate('date', today())->first();
        $peutMarquer = Calendar::estJourTravaille(now()) && !$presenceAujourdhui;

        // Récupérer la configuration des horaires pour affichage
        $config = ConfigJoursTravail::first();
        $heureDebut = $config ? $config->heure_debut : '09:00:00';
        $heureFin   = $config ? $config->heure_fin : '18:15:00';

        return view('stagiaire.presence.index', compact(
            'presences', 'permissions', 'presenceAujourdhui', 'peutMarquer',
            'heureDebut', 'heureFin'
        ));
    }

    public function marquer(Request $request)
    {
        $stagiaire = Auth::user()->stagiaire;

        // Vérifier si c'est un jour travaillé
        abort_unless(Calendar::estJourTravaille(now()), 403, 'Pas de marquage ce jour.');

        // Éviter le double marquage
        $existant = Presence::where('stagiaire_id', $stagiaire->id)
            ->whereDate('date', today())
            ->first();

        if ($existant) {
            return back()->with('erreur', 'Présence déjà marquée aujourd\'hui.');
        }

        // Récupérer la configuration des horaires
        $config = ConfigJoursTravail::first();
        if (!$config) {
            $heureDebut = '09:00:00';
            $heureFin   = '18:15:00';
        } else {
            $heureDebut = $config->heure_debut;
            $heureFin   = $config->heure_fin;
        }

        $heureArrivee = now()->format('H:i:s');

        // Déterminer le statut selon les règles
        if ($heureArrivee <= $heureDebut) {
            $statut = 'present';
        } elseif ($heureArrivee > $heureDebut && $heureArrivee <= $heureFin) {
            $statut = 'retard';
        } else { // après l'heure de fin
            $statut = 'absent';
        }

        $request->validate([
            'motif'        => 'nullable|string|max:500',
            'justificatif' => 'nullable|file|max:10240',
        ]);

        $justificatif = null;
        if ($request->hasFile('justificatif')) {
            $justificatif = $request->file('justificatif')->store('justificatifs', 'public');
        }

        Presence::create([
            'stagiaire_id' => $stagiaire->id,
            'date'         => today(),
            'statut'       => $statut,
            'motif'        => $request->motif,
            'justificatif' => $justificatif,
            'heure_arrivee'=> $heureArrivee,
        ]);

        return back()->with('succes', 'Présence marquée : ' . $statut);
    }

    public function demandePermission(Request $request)
    {
        $request->validate([
            'date_debut'   => 'required|date|after:' . now()->addHours(24)->toDateString(),
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'motif'        => 'required|string|max:500',
            'justificatif' => 'nullable|file|max:10240',
        ]);

        $stagiaire    = Auth::user()->stagiaire;
        $justificatif = null;
        if ($request->hasFile('justificatif')) {
            $justificatif = $request->file('justificatif')->store('permissions', 'public');
        }

        Permission::create([
            'stagiaire_id' => $stagiaire->id,
            'date_debut'   => $request->date_debut,
            'date_fin'     => $request->date_fin,
            'motif'        => $request->motif,
            'justificatif' => $justificatif,
            'statut'       => 'en_attente',
        ]);

        return back()->with('succes', 'Demande de permission soumise. En attente de validation.');
    }

    public function show(Presence $presence)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($presence->stagiaire_id !== $stagiaire->id, 403);
        return view('stagiaire.presence.show', compact('presence'));
    }

    public function telechargerJustificatif(Presence $presence)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($presence->stagiaire_id !== $stagiaire->id && !Auth::user()->isAdmin(), 403);
        abort_if(!$presence->justificatif, 404);
        $chemin = storage_path('app/public/' . $presence->justificatif);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }
}
