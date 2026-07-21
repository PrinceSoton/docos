<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'stagiaire_id', 'titre', 'description', 'cree_par',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function fichiers()
    {
        return $this->hasMany(ArchiveFichier::class);
    }
}