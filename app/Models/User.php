<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone',
        'photo', 'password', 'role', 'actif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['actif' => 'boolean'];

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function stagiaire()
    {
        return $this->hasOne(Stagiaire::class);
    }

    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function documentsPartages()
    {
        return $this->belongsToMany(Document::class, 'document_partage');
    }

    public function evenements()
    {
        return $this->hasMany(Evenement::class, 'cree_par');
    }

    public function evenementsRecus()
    {
        return $this->belongsToMany(Evenement::class, 'evenement_user');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    public function isStagiaire(): bool
    {
        return $this->role === 'stagiaire';
    }
}