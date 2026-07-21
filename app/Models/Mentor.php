<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'departement', 'poste', 'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class, 'mentor_id', 'user_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'mentor_id', 'user_id');
    }
}