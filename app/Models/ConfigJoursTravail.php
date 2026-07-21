<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigJoursTravail extends Model
{
    use HasFactory;

    protected $table = 'config_jours_travail';

    protected $fillable = [
        'lundi', 'mardi', 'mercredi', 'jeudi',
        'vendredi', 'samedi', 'dimanche',
        'heure_debut', 'heure_fin',
    ];

    protected $casts = [
        'lundi'    => 'boolean',
        'mardi'    => 'boolean',
        'mercredi' => 'boolean',
        'jeudi'    => 'boolean',
        'vendredi' => 'boolean',
        'samedi'   => 'boolean',
        'dimanche' => 'boolean',
    ];
}