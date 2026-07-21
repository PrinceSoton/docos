<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attestation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id', 'type', 'statut', 'motif_demande',
        'fichier', 'valide_par_mentor', 'valide_le_mentor',
        'envoye_par_admin', 'envoye_le', 'commentaire', 'demande_effectuee',
    ];

    protected $casts = [
        'valide_le_mentor'  => 'datetime',
        'envoye_le'         => 'datetime',
        'demande_effectuee' => 'boolean',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par_mentor');
    }

    public function envoyePar()
    {
        return $this->belongsTo(User::class, 'envoye_par_admin');
    }
}