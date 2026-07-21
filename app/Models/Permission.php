<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = [
        'stagiaire_id', 'date_debut', 'date_fin', 'motif',
        'justificatif', 'statut', 'valide_par',
        'commentaire_mentor', 'demande_le',
    ];

    protected $casts = [
        'date_debut'  => 'date',
        'date_fin'    => 'date',
        'demande_le'  => 'datetime',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function peutEtreDemandee(): bool
    {
        return now()->addHours(24)->lessThanOrEqualTo($this->date_debut->startOfDay());
    }
}