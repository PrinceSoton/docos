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
        'motif', 'justificatif', 'heure_arrivee',
    ];

    protected $casts = ['date' => 'date'];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    /**
     * Synchronise les présences pour un stagiaire donné.
     * Ajoute les absences manquantes pour les jours travaillés non pointés.
     */
    public static function syncAbsencesForStagiaire(Stagiaire $stagiaire): void
    {
        $today = Carbon::today();
        $dateDebut = $stagiaire->date_debut;
        $dateFin = $stagiaire->date_fin;

        $current = $dateDebut->copy()->startOfDay();

        while ($current->lte($today) && $current->lte($dateFin)) {
            // Vérifier si c'est un jour travaillé
            if (Calendar::estJourTravaille($current)) {
                // Vérifier si une présence existe déjà pour ce stagiaire à cette date
                $exists = self::where('stagiaire_id', $stagiaire->id)
                    ->whereDate('date', $current)
                    ->exists();

                if (!$exists) {
                    // Créer une absence automatique
                    self::create([
                        'stagiaire_id' => $stagiaire->id,
                        'date'         => $current,
                        'statut'       => 'absent',
                        'motif'        => 'Absence automatique (non pointé)',
                        'heure_arrivee'=> null,
                    ]);
                }
            }
            $current->addDay();
        }
    }

    /**
     * Synchronise les présences pour tous les stagiaires actifs.
     */
    public static function syncAbsencesForAllStagiaires(): void
    {
        $stagiaires = Stagiaire::where('statut', 'en_cours')->get();
        foreach ($stagiaires as $stagiaire) {
            self::syncAbsencesForStagiaire($stagiaire);
        }
    }
}
