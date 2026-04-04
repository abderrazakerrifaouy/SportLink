<?php

namespace App\Repository;

use App\Models\Follow;

class FollowRepository
{
    public function create($data)
    {
        if (Follow::where('follower_id', $data['follower_id'])->where('following_id', $data['following_id'])->exists()) {
            return null; 
        }
        return Follow::create($data);
    }

    public function delete($id)
    {
        $follow = Follow::find($id);
        if ($follow) {
            $follow->delete();
            return true;
        }
        return false;
    }

    public function getFollowers($userId)
    {
        return Follow::where('following_id', $userId)->with('follower')->get();
    }

    public function getFollowing($userId)
    {
        return Follow::where('follower_id', $userId)->with('following')->get();
    }

    public function countFollowers($userId)
    {
        return Follow::where('following_id', $userId)->count();
    }

    public function countFollowing($userId): int
    {
        return Follow::where('follower_id', $userId)->count();
    }

    public function isFollowing($followerId, $followingId)
    {
        return Follow::where('follower_id', $followerId)->where('following_id', $followingId)->exists();
    }
}