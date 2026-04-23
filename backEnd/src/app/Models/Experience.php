<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model {
    protected $fillable = ['idClub', 'image', 'joinDate', 'endDate', 'place', 'categoryType', 'joueur_id'];

    public function joueur() { return $this->belongsTo(Joueur::class); }

    public function clubAdmin() { return $this->belongsTo(ClubAdmin::class, 'idClub'); }

    public function request() { return $this->hasOne(ClubJoueurRequest::class, 'experience_id'); }
}
