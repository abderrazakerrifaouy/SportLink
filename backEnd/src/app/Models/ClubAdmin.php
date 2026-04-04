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
}
