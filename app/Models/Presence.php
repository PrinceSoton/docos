<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}