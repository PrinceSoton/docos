<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'titre', 'description', 'fichier',
        'type_fichier', 'taille', 'partage_tous',
    ];

    protected $casts = ['partage_tous' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partagesAvec()
    {
        return $this->belongsToMany(User::class, 'document_partage');
    }

    public function estAccessiblePar(User $user): bool
    {
        if ($this->user_id === $user->id) return true;
        if ($this->partage_tous) return true;
        return $this->partagesAvec()->where('user_id', $user->id)->exists();
    }
}