<?php

namespace App\Http\Controllers\Mentor;

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
    /*  Index : liste complète + filtres                                   */
    /* ------------------------------------------------------------------ */

    public function index(Request $request)
    {
        $mentorId = Auth::id();

        // Liste des stagiaires du mentor (pour le select + sécurité)
        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', $mentorId)
            ->get();

        $stagiaireIds = $stagiaires->pluck('id');

        // --- Requête de base : toutes les présences des stagiaires du mentor ---
        $query = Presence::with('stagiaire.user', 'marquePar')
            ->whereIn('stagiaire_id', $stagiaireIds);

        $stagiaire = null;

        // --- Filtre par stagiaire (avec vérification d'appartenance) ---
        if ($request->filled('stagiaire_id')) {
            $stagiaire = Stagiaire::with('user')->findOrFail($request->stagiaire_id);
            abort_if($stagiaire->mentor_id !== $mentorId, 403);
            $query->where('stagiaire_id', $stagiaire->id);
        }

        // --- Filtres temporels (priorité : date > mois > année) ---
        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('date', $date);
        } elseif ($request->filled('mois')) {
            $mois = Carbon::parse($request->mois . '-01');
            $query->whereYear('date', $mois->year)
                  ->whereMonth('date', $mois->month);
        } elseif ($request->filled('annee')) {
            $query->whereYear('date', (int) $request->annee);
        }

        // --- Pagination avec conservation des filtres ---
        $presences = $query->orderBy('date', 'desc')
            ->paginate(30)
            ->withQueryString();

        // --- Statistiques du stagiaire sélectionné (facultatif) ---
        $statsStagiaire = null;
        if ($stagiaire) {
            $statsStagiaire = [
                'present' => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'present')->count(),
                'retard'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'retard')->count(),
                'absent'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'absent')->count(),
            ];
        }

        // --- Permissions en attente pour le bloc de validation ---
        $permissionsEnAttente = Permission::with('stagiaire.user')
            ->whereIn('stagiaire_id', $stagiaireIds)
            ->where('statut', 'en_attente')
            ->get();

        return view('mentor.presences.index', compact(
            'stagiaires',
            'stagiaire',
            'presences',
            'statsStagiaire',
            'permissionsEnAttente'
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  Show : fiche complète d'un stagiaire                               */
    /* ------------------------------------------------------------------ */

    public function show(Stagiaire $stagiaire)
    {
        abort_if($stagiaire->mentor_id !== Auth::id(), 403);

        $presences = Presence::with('marquePar')
            ->where('stagiaire_id', $stagiaire->id)
            ->orderBy('date', 'desc')->paginate(30);

        $permissions = Permission::where('stagiaire_id', $stagiaire->id)
            ->orderBy('date_debut', 'desc')->get();

        $stats = [
            'present' => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'present')->count(),
            'retard'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'retard')->count(),
            'absent'  => Presence::where('stagiaire_id', $stagiaire->id)->where('statut', 'absent')->count(),
        ];

        return view('mentor.presences.show', compact('stagiaire', 'presences', 'permissions', 'stats'));
    }

    /* ------------------------------------------------------------------ */
    /*  Validation des permissions                                         */
    /* ------------------------------------------------------------------ */

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

    /* ------------------------------------------------------------------ */
    /*  Marquage manuel par lot                                            */
    /* ------------------------------------------------------------------ */

    public function formMarquage(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        if ($date->isAfter(Carbon::today())) {
            $date = Carbon::today();
        }

        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', Auth::id())
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

        return view('mentor.presences.marquer', compact(
            'stagiaires', 'presencesExistantes', 'date', 'estJourTravaille'
        ));
    }

    public function marquerLot(Request $request)
    {
        $request->validate([
            'date'                     => 'required|date|before_or_equal:today',
            'presences'                => 'required|array|min:1',
            'presences.*.stagiaire_id' => 'required|exists:stagiaires,id',
            'presences.*.statut'       => 'required|in:present,retard,absent',
            'presences.*.motif'        => 'nullable|string|max:500',
        ]);

        $date  = Carbon::parse($request->date);
        $count = 0;

        DB::transaction(function () use ($request, $date, &$count) {
            foreach ($request->presences as $data) {
                $stagiaire = Stagiaire::find($data['stagiaire_id']);
                if (!$stagiaire) continue;

                // Sécurité : le mentor ne peut marquer que ses propres stagiaires
                if ($stagiaire->mentor_id !== Auth::id()) continue;

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
            ->route('mentor.presences.index')
            ->with('succes', "{$count} présence(s) enregistrée(s) pour le {$date->translatedFormat('d/m/Y')}.");
    }
}