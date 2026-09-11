<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'date_debut', 'date_fin',
        'statut', 'priorite', 'mentor_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    /** Créateur du projet */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /** Tous les mentors du projet (créateur + collaborateurs) */
    public function mentors()
    {
        return $this->belongsToMany(User::class, 'project_mentor')->withTimestamps();
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'project_stagiaire');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /** Vérifie qu'un utilisateur a accès au projet */
    public function hasAccess(User $user): bool
    {
        return $this->mentors()->where('user_id', $user->id)->exists();
    }

    /** Vérifie que l'utilisateur est le créateur */
    public function isOwner(User $user): bool
    {
        return $this->mentor_id === $user->id;
    }

    public function progressionPourcent(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $terminees = $this->tasks()->where('statut', 'termine')->count();
        return (int) round(($terminees / $total) * 100);
    }
}