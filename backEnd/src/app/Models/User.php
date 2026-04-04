<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'bio', 'profileImage', 'bannerImage', 'role', 'isActive'];

    // Relation 1:1 m3a Joueur
    public function joueur(): HasOne {
        return $this->hasOne(Joueur::class);
    }

    // Relation 1:1 m3a ClubAdmin
    public function clubAdmin(): HasOne {
        return $this->hasOne(ClubAdmin::class);
    }

    public function posts(): HasMany {
        return $this->hasMany(Post::class);
    }

    public function messagesSent() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function followers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function following(): BelongsToMany {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }
}