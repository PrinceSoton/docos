<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'matricule', 'ecole', 'niveau_etude',
        'specialite', 'date_debut', 'date_fin', 'cv',
        'description', 'statut', 'mentor_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // Génération automatique du matricule
    public static function genererMatricule(): string
    {
        $annee = date('Y');
        $dernier = self::whereYear('created_at', $annee)->count() + 1;
        return 'STG-' . $annee . '-' . str_pad($dernier, 2, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_stagiaire');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function dureeStageDays(): int
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }
}