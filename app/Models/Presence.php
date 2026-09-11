<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id', 'date', 'statut',
        'motif', 'justificatif',
        'heure_arrivee', 'heure_depart',
        'marque_par',
    ];

    protected $casts = ['date' => 'date'];

    /* ------------------------------------------------------------------ */
    /*  Relations                                                          */
    /* ------------------------------------------------------------------ */

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function marquePar()
    {
        return $this->belongsTo(User::class, 'marque_par');
    }

    /* ------------------------------------------------------------------ */
    /*  Marquage manuel par Admin ou Mentor                                */
    /* ------------------------------------------------------------------ */

    /**
     * Marque ou corrige la présence d'un stagiaire.
     * Toute la logique métier est centralisée ici.
     *
     * @param Stagiaire $stagiaire  Le stagiaire concerné
     * @param Carbon    $date       La date à marquer
     * @param string    $statut     'present' | 'retard' | 'absent'
     * @param string|null $motif    Motif éventuel (obligatoire pour absent)
     * @param int       $userId     ID de l'admin ou mentor qui marque
     */
    public static function marquerParUtilisateur(
        Stagiaire $stagiaire,
        Carbon $date,
        string $statut,
        ?string $motif,
        int $userId
    ): self {
        $existing = self::where('stagiaire_id', $stagiaire->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        $config     = ConfigJoursTravail::first();
        $heureDebut = $config?->heure_debut ?? '09:00:00';

        /* ---------------------------------------------------------- */
        /*  Calcul de heure_arrivee selon statut                       */
        /* ---------------------------------------------------------- */
        $heureArrivee = $existing?->heure_arrivee;

        if ($statut === 'absent') {
            $heureArrivee = null;
        } elseif (!$heureArrivee) {
            $heureArrivee = $statut === 'present'
                ? $heureDebut
                : Carbon::parse($heureDebut)->addHour()->format('H:i:s');
        }

        // Motif par défaut si absent et non fourni
        if ($statut === 'absent' && blank($motif)) {
            $motif = 'Absence marquée manuellement';
        }

        return self::updateOrCreate(
            [
                'stagiaire_id' => $stagiaire->id,
                'date'         => $date->toDateString(),
            ],
            [
                'statut'        => $statut,
                'motif'         => $motif,
                'heure_arrivee' => $heureArrivee,
                'heure_depart'  => $statut === 'absent' ? null : $existing?->heure_depart,
                'marque_par'    => $userId,
            ]
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Synchronisation automatique (existante)                            */
    /* ------------------------------------------------------------------ */

    public static function syncAbsencesForStagiaire(Stagiaire $stagiaire): void
    {
        $today     = Carbon::today();
        $dateDebut = $stagiaire->date_debut;
        $dateFin   = $stagiaire->date_fin;

        $current = $dateDebut->copy()->startOfDay();

        while ($current->lte($today) && $current->lte($dateFin)) {
            if (Calendar::estJourTravaille($current)) {
                $exists = self::where('stagiaire_id', $stagiaire->id)
                    ->whereDate('date', $current)
                    ->exists();

                if (!$exists) {
                    self::create([
                        'stagiaire_id'  => $stagiaire->id,
                        'date'          => $current,
                        'statut'        => 'absent',
                        'motif'         => 'Absence automatique (non pointé)',
                        'heure_arrivee' => null,
                        'heure_depart'  => null,
                        'marque_par'    => null,
                    ]);
                }
            }
            $current->addDay();
        }
    }

    public static function syncAbsencesForAllStagiaires(): void
    {
        $stagiaires = Stagiaire::where('statut', 'en_cours')->get();
        foreach ($stagiaires as $stagiaire) {
            self::syncAbsencesForStagiaire($stagiaire);
        }
    }
}