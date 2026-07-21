<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use Illuminate\Support\Facades\Auth;

class EvenementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $evenements = Evenement::where('partage_tous', true)
            ->orWhereHas('utilisateurssCibles', fn($q) => $q->where('user_id', $user->id))
            ->latest()->paginate(15);
        return view('mentor.evenements.index', compact('evenements'));
    }

    public function show(Evenement $evenement)
    {
        abort_unless($evenement->estVisiblePar(Auth::user()), 403);
        return view('mentor.evenements.show', compact('evenement'));
    }
}