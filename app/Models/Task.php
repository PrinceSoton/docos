<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'stagiaire_id', 'titre', 'description',
        'statut', 'priorite', 'difficulte', 'date_echeance',
    ];

    protected $casts = ['date_echeance' => 'date'];

  
    /*  Relations                                                          */

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

   
    /*  Helpers                                                            */
   
    /** Vrai si la tâche n'est rattachée à aucun projet */
    public function estIndependante(): bool
    {
        return is_null($this->project_id);
    }

    /** Vrai si la tâche est rattachée à un projet */
    public function estLieeAProjet(): bool
    {
        return !$this->estIndependante();
    }

    
    /** Scope : tâches indépendantes (sans projet) */
    public function scopeIndependantes($query)
    {
        return $query->whereNull('project_id');
    }

    /** Scope : tâches liées à un projet */
    public function scopeLieesAProjet($query)
    {
        return $query->whereNotNull('project_id');
    }


    /*  Badges (existants)*/
  
    public function badgePriorite(): string
    {
        return match ($this->priorite) {
            'haute'   => 'danger',
            'urgente' => 'dark',
            'normale' => 'warning',
            default   => 'success',
        };
    }

    public function badgeDifficulte(): string
    {
        return match ($this->difficulte) {
            'difficile' => 'danger',
            'moyen'     => 'warning',
            default     => 'success',
        };
    }
}