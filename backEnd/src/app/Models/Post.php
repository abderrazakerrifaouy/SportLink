<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    protected $fillable = ['content', 'user_id'];

    public function user() { return $this->belongsTo(User::class); }
    
    public function comments() { return $this->hasMany(Comment::class); }

    // Media Polymorphic (Images/Videos)
    public function media() { return $this->morphMany(Media::class, 'mediable'); }

    // Reactions Polymorphic (Like, Love...)
    public function reactions() { return $this->morphMany(Reaction::class, 'reactable'); }
}
