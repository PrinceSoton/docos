<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Presence;
use App\Models\Permission;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresenceController extends Controller
{
    /* ------------------------------------------------------------------ */
    /*  Index / Show (existants)                                           */
    /* ------------------------------------------------------------------ */

    public function index(Request $request)
    {
        $stagiaires = Stagiaire::with('user')->where('statut', 'en_cours')->get();
        $stagiaire  = null;
        $query = Presence::with('stagiaire.user', 'marquePar');

        if ($request->filled('stagiaire_id')) {
            $stagiaire = Stagiaire::with('user')->findOrFail($request->stagiaire_id);
            $query->where('stagiaire_id', $stagiaire->id);
        }

        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('date', $date);
        } elseif ($request->filled('mois')) {
            $mois = Carbon::parse($request->mois . '-01');
            $query->whereYear('date', $mois->year)->whereMonth('date', $mois->month);
        } elseif ($request->filled('annee')) {
            $query->whereYear('date', (int) $request->annee);
        }

        $presences = $query->orderBy('date', 'desc')->paginate(30)->withQueryString();

        return view('admin.presences.index', compact('stagiaires', 'stagiaire', 'presences'));
    }

    public function show(Stagiaire $stagiaire)
    {
        $presences   = Presence::with('marquePar')
            ->where('stagiaire_id', $stagiaire->id)
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

    /* ------------------------------------------------------------------ */
    /*  Marquage manuel par lot (Admin)                                    */
    /* ------------------------------------------------------------------ */

    /** Affiche le formulaire de marquage pour une date donnée */
    public function formMarquage(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        // Empêcher les dates futures
        if ($date->isAfter(Carbon::today())) {
            $date = Carbon::today();
        }

        $stagiaires = Stagiaire::with('user')
            ->where('statut', 'en_cours')
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->orderBy('matricule')
            ->get();

        $presencesExistantes = Presence::whereDate('date', $date->toDateString())
            ->whereIn('stagiaire_id', $stagiaires->pluck('id'))
            ->get()
            ->keyBy('stagiaire_id');

        $estJourTravaille = Calendar::estJourTravaille($date);

        return view('admin.presences.marquer', compact(
            'stagiaires', 'presencesExistantes', 'date', 'estJourTravaille'
        ));
    }

    /** Enregistre les présences en lot */
    public function marquerLot(Request $request)
    {
        $request->validate([
            'date'                      => 'required|date|before_or_equal:today',
            'presences'                 => 'required|array|min:1',
            'presences.*.stagiaire_id'  => 'required|exists:stagiaires,id',
            'presences.*.statut'        => 'required|in:present,retard,absent',
            'presences.*.motif'         => 'nullable|string|max:500',
        ]);

        $date = Carbon::parse($request->date);
        $count = 0;

        DB::transaction(function () use ($request, $date, &$count) {
            foreach ($request->presences as $data) {
                $stagiaire = Stagiaire::find($data['stagiaire_id']);
                if (!$stagiaire) continue;

                Presence::marquerParUtilisateur(
                    $stagiaire,
                    $date,
                    $data['statut'],
                    $data['motif'] ?? null,
                    Auth::id()
                );
                $count++;
            }
        });

        return redirect()
            ->route('admin.presences.index', ['date' => $date->toDateString()])
            ->with('succes', "{$count} présence(s) enregistrée(s) pour le {$date->translatedFormat('d/m/Y')}.");
    }
}