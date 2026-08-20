<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Attestation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttestationController extends Controller
{
    public function index()
    {
        $stagiaire    = Auth::user()->stagiaire;
        $attestations = Attestation::where('stagiaire_id', $stagiaire->id)->latest()->get();
        return view('stagiaire.attestations.index', compact('attestations'));
    }

    public function request()
    {
        $stagiaire = Auth::user()->stagiaire;
        // Vérifier si demande déjà effectuée pour chaque type
        $attestationExiste  = Attestation::where('stagiaire_id', $stagiaire->id)->where('type', 'attestation')->exists();
        $conventionExiste   = Attestation::where('stagiaire_id', $stagiaire->id)->where('type', 'convention')->exists();
        return view('stagiaire.attestations.request', compact('attestationExiste', 'conventionExiste', 'stagiaire'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:attestation,convention',
            'motif_demande' => 'nullable|string|max:500',
        ]);

        $stagiaire = Auth::user()->stagiaire;

        // Une seule demande par type
        $existant = Attestation::where('stagiaire_id', $stagiaire->id)->where('type', $request->type)->first();
        if ($existant) {
            return back()->withErrors(['type' => 'Vous avez déjà effectué cette demande.']);
        }

        Attestation::create([
            'stagiaire_id'     => $stagiaire->id,
            'type'             => $request->type,
            'motif_demande'    => $request->motif_demande,
            'statut'           => 'en_attente',
            'demande_effectuee'=> true,
        ]);

        return redirect()->route('stagiaire.attestations.index')->with('succes', 'Demande soumise avec succès.');
    }

    public function telecharger(Attestation $attestation)
    {
        $stagiaire = Auth::user()->stagiaire;
        abort_if($attestation->stagiaire_id !== $stagiaire->id, 403);
        abort_if(!$attestation->fichier, 404, 'Aucun document disponible.');
        $chemin = storage_path('app/public/' . $attestation->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }
}
