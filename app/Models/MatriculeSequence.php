<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatriculeSequence extends Model
{
    protected $primaryKey = 'annee';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'annee',
        'dernier_numero'
    ];

    protected $casts = [
        'annee' => 'integer',
        'dernier_numero' => 'integer'
    ];
}