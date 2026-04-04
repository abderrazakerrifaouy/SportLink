<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Titre extends Model {
    protected $fillable = ['nomTitre', 'description', 'number', 'club_admin_id'];
    public function club() { return $this->belongsTo(ClubAdmin::class, 'club_admin_id'); }
}
