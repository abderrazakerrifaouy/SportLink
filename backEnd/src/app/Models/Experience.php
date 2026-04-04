<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model {
    protected $fillable = ['nomClub', 'joinDate', 'endDate', 'place', 'categoryType', 'joueur_id'];
    public function joueur() { return $this->belongsTo(Joueur::class); }
}
