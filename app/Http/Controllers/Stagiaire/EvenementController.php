<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvenementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $evenements = Evenement::where('partage_tous', true)
            ->orWhereHas('utilisateurssCibles', fn($q) => $q->where('user_id', $user->id))
            ->latest()->paginate(15);
        return view('stagiaire.evenements.index', compact('evenements'));
    }

    public function show(Evenement $evenement)
    {
        abort_unless($evenement->estVisiblePar(Auth::user()), 403);
        return view('stagiaire.evenements.show', compact('evenement'));
    }

    public function telecharger(Evenement $evenement)
    {
        abort_unless($evenement->estVisiblePar(Auth::user()), 403);
        abort_if(!$evenement->fichier, 404);
        $chemin = storage_path('app/public/' . $evenement->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }
}