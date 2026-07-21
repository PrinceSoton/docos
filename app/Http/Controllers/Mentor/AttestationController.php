<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Attestation;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttestationController extends Controller
{
    public function index()
    {
        $stagiaires = Stagiaire::where('mentor_id', Auth::id())->pluck('id');
        $attestations = Attestation::with('stagiaire.user')
            ->whereIn('stagiaire_id', $stagiaires)->latest()->paginate(15);
        return view('mentor.attestations.index', compact('attestations'));
    }

    public function show(Attestation $attestation)
    {
        abort_if($attestation->stagiaire->mentor_id !== Auth::id(), 403);
        return view('mentor.attestations.show', compact('attestation'));
    }

    public function validate(Attestation $attestation)
    {
        abort_if($attestation->stagiaire->mentor_id !== Auth::id(), 403);
        return view('mentor.attestations.validate', compact('attestation'));
    }

    public function doValidate(Request $request, Attestation $attestation)
    {
        abort_if($attestation->stagiaire->mentor_id !== Auth::id(), 403);
        $request->validate([
            'statut'      => 'required|in:valide_mentor,refuse',
            'commentaire' => 'nullable|string',
        ]);

        $attestation->update([
            'statut'             => $request->statut,
            'valide_par_mentor'  => Auth::id(),
            'valide_le_mentor'   => now(),
            'commentaire'        => $request->commentaire,
        ]);

        $msg = $request->statut === 'valide_mentor' ? 'Demande validée.' : 'Demande refusée.';
        return redirect()->route('mentor.attestations.index')->with('succes', $msg);
    }

    public function telecharger(Attestation $attestation)
    {
        abort_if($attestation->stagiaire->mentor_id !== Auth::id(), 403);
        abort_if(!$attestation->fichier, 404, 'Aucun fichier.');
        $chemin = storage_path('app/public/' . $attestation->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }
}