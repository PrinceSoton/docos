<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id', 'project_id', 'titre', 'description',
        'fichier', 'type', 'type_autre', 'statut', 'note',
        'commentaire_mentor', 'valide_par', 'valide_le',
    ];

    protected $casts = ['valide_le' => 'datetime'];

    public function getTypeAfficheAttribute(): string
    {
        return $this->type === 'autre' && $this->type_autre
            ? $this->type_autre
            : $this->type;
    }

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
