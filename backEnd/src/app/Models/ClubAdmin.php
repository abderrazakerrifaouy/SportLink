<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubAdmin extends Model
{
    protected $fillable = ['user_id', 'nomClub', 'description', 'ecole', 'tactique', 'gestion'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function titres()
    {
        return $this->hasMany(Titre::class);
    }

    public function joueurs()
    {
        return $this->belongsToMany(Joueur::class, 'club_joueur_requests')
                    ->withPivot('status')
                    ->wherePivot('status', 'ACCEPTED');
    }

    public function clubJoueurRequests()
    {
        return $this->hasMany(ClubJoueurRequest::class);
    }
}
