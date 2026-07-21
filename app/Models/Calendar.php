<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'libelle', 'type', 'description', 'cree_par',
    ];

    protected $casts = ['date' => 'date'];

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    // Vérifie si une date est un jour travaillé
    public static function estJourTravaille(\Carbon\Carbon $date): bool
    {
        // Vérifie si c'est un jour férié ou séjour
        if (self::where('date', $date->toDateString())->exists()) {
            return false;
        }

        $config = ConfigJoursTravail::first();
        if (!$config) return !in_array($date->dayOfWeek, [0, 6]);

        $joursSemaine = [
            0 => 'dimanche',
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
        ];

        $jour = $joursSemaine[$date->dayOfWeek];
        return (bool) $config->{$jour};
    }
}