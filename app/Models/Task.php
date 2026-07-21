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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

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