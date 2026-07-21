<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveFichier extends Model
{
    use HasFactory;

    protected $table = 'archive_fichiers';

    protected $fillable = [
        'archive_id', 'nom_original', 'chemin',
        'type_fichier', 'taille',
    ];

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }
}