<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Presence;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $stagiaires = Stagiaire::with('user')->where('mentor_id', Auth::id())->get();
        $stagiaire  = null;
        $presences  = collect();

        if ($request->filled('stagiaire_id')) {
            $stagiaire = Stagiaire::findOrFail($request->stagiaire_id);
            abort_if($stagiaire->mentor_id !== Auth::id(), 403);
            $presences = Presence::where('stagiaire_id', $stagiaire->id)
                ->orderBy('date', 'desc')->paginate(30);
        }

        $permissionsEnAttente = Permission::with('stagiaire.user')
            ->whereIn('stagiaire_id', $stagiaires->pluck('id'))
            ->where('statut', 'en_attente')->get();

        return view('mentor.presences.index', compact('stagiaires', 'stagiaire', 'presences', 'permissionsEnAttente'));
    }

    public function show(Stagiaire $stagiaire)
    {
        abort_if($stagiaire->mentor_id !== Auth::id(), 403);
        $presences = Presence::where('stagiaire_id', $stagiaire->id)->orderBy('date', 'desc')->paginate(30);
        $permissions = Permission::where('stagiaire_id', $stagiaire->id)->orderBy('date_debut', 'desc')->get();
        $stats = [
            'present' => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'present')->count(),
            'retard'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'retard')->count(),
            'absent'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'absent')->count(),
        ];
        return view('mentor.presences.show', compact('stagiaire', 'presences', 'permissions', 'stats'));
    }

    public function validerPermission(Request $request, Permission $permission)
    {
        $stagiaire = $permission->stagiaire;
        abort_if($stagiaire->mentor_id !== Auth::id(), 403);

        $request->validate([
            'statut'             => 'required|in:valide,refuse',
            'commentaire_mentor' => 'nullable|string',
        ]);

        $permission->update([
            'statut'             => $request->statut,
            'valide_par'         => Auth::id(),
            'commentaire_mentor' => $request->commentaire_mentor,
        ]);

        $msg = $request->statut === 'valide' ? 'Permission validée.' : 'Permission refusée.';
        return back()->with('succes', $msg);
    }
}