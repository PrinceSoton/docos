<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'matricule', 'ecole', 'niveau_etude',
        'specialite', 'date_debut', 'date_fin', 'cv',
        'description', 'statut', 'mentor_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // Génération automatique du matricule
    public static function genererMatricule(): string
    {
        $annee = (int) date('Y');

        return DB::transaction(function () use ($annee): string {
            // 1. Verrouiller la ligne pour mise à jour exclusive
            $sequence = MatriculeSequence::where('annee', $annee)
                ->lockForUpdate()
                ->first();

            // 2. Si la séquence n’existe pas, l’initialiser avec le dernier matricule utilisé
            if (!$sequence) {
                $max = self::whereYear('created_at', $annee)
                    ->get()
                    ->map(function ($s) use ($annee) {
                        preg_match('/STG-' . $annee . '-(\d+)/', $s->matricule, $matches);
                        return isset($matches[1]) ? (int)$matches[1] : 0;
                    })
                    ->max() ?? 0;

                $sequence = MatriculeSequence::create([
                    'annee'   => $annee,
                    'dernier_numero' => $max,
                ]);
            }

            // 3. Incrémenter le compteur
            $sequence->increment('dernier_numero');
            $sequence->refresh(); // ← maintenant sur une instance de modèle, OK

            $numero = $sequence->dernier_numero;

            return 'STG-' . $annee . '-' . str_pad((string) $numero, 2, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_stagiaire');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function dureeStageDays(): int
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }
}