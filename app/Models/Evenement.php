<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'cree_par', 'titre', 'contenu', 'image', 'fichier',
        'type', 'partage_tous', 'date_evenement',
    ];

    protected $casts = [
        'partage_tous'   => 'boolean',
        'date_evenement' => 'datetime',
    ];

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function utilisateurssCibles()
    {
        return $this->belongsToMany(User::class, 'evenement_user');
    }

    public function estVisiblePar(User $user): bool
    {
        if ($this->partage_tous) return true;
        return $this->utilisateurssCibles()->where('user_id', $user->id)->exists();
    }
}