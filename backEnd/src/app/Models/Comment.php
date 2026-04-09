<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
}
