<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubJoueurRequest extends Model {
    protected $fillable = ['joueur_id', 'club_admin_id', 'status', 'experience_id'];

    public function joueur() { return $this->belongsTo(Joueur::class); }

    public function club() { return $this->belongsTo(ClubAdmin::class, 'club_admin_id'); }

    public function experience() { return $this->belongsTo(Experience::class); }
}
