<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attestation;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttestationManagementController extends Controller
{
    public function index()
    {
        $attestations = Attestation::with('stagiaire.user', 'validePar', 'envoyePar')
            ->latest()->paginate(15);
        return view('admin.attestations.index', compact('attestations'));
    }

    public function show(Attestation $attestation)
    {
        $attestation->load('stagiaire.user', 'validePar', 'envoyePar');
        return view('admin.attestations.show', compact('attestation'));
    }

    public function uploadForm(Attestation $attestation)
    {
        abort_if(!in_array($attestation->statut, ['valide_mentor', 'approuve_admin']), 403, 'Action non autorisée.');
        return view('admin.attestations.upload', compact('attestation'));
    }

    public function upload(Request $request, Attestation $attestation)
    {
        $request->validate([
            'fichier'     => 'required|file|max:20480',
            'commentaire' => 'nullable|string',
        ]);

        if ($attestation->fichier) {
            Storage::disk('public')->delete($attestation->fichier);
        }

        $chemin = $request->file('fichier')->store('attestations', 'public');

        $attestation->update([
            'fichier'         => $chemin,
            'statut'          => 'envoye',
            'envoye_par_admin'=> Auth::id(),
            'envoye_le'       => now(),
            'commentaire'     => $request->commentaire,
        ]);

        return redirect()->route('admin.attestations.index')->with('succes', 'Attestation envoyée au stagiaire.');
    }

    public function telecharger(Attestation $attestation)
    {
        abort_if(!$attestation->fichier, 404, 'Aucun fichier disponible.');
        $chemin = storage_path('app/public/' . $attestation->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }

    public function destroy(Attestation $attestation)
    {
        if ($attestation->fichier) Storage::disk('public')->delete($attestation->fichier);
        $attestation->delete();
        return redirect()->route('admin.attestations.index')->with('succes', 'Demande supprimée.');
    }
}