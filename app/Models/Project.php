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

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
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

    public function progressionPourcent(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $terminees = $this->tasks()->where('statut', 'termine')->count();
        return (int) round(($terminees / $total) * 100);
    }
}