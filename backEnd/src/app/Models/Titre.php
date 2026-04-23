<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Titre extends Model {
    protected $fillable = ['nomTitre', 'description', 'number', 'club_admin_id'];
    protected $attributes = [
        'number' => 1,
    ];
    protected $casts = [
        'number' => 'integer',
    ];

    public function club() { return $this->belongsTo(ClubAdmin::class, 'club_admin_id'); }
}
