<?php

namespace App\Services;

use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\FollowRepository;

class UserService
{
    protected $userRepo;
    protected $messageRepo;
    protected $followRepo;

    public function __construct(UserRepository $userRepo, MessageRepository $messageRepo, FollowRepository $followRepo)
    {
        $this->userRepo = $userRepo;
        $this->messageRepo = $messageRepo;
        $this->followRepo = $followRepo;
    }

    public function find(Int $id)
    {
        return $this->userRepo->find($id);
    }

    public function update($id, $data)
    {
        return $this->userRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->userRepo->delete($id);
    }

    public function all()
    {
        return $this->userRepo->all();
    }

    public function findByUsername($username)
    {
        return $this->userRepo->findByUsername($username);
    }

    public function SendMessage($senderId, $receiverId, $message)
    {
        return $this->messageRepo->create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message
        ]);
    }

    public function getConversation($userId1, $userId2)
    {
        return $this->messageRepo->getConversation($userId1, $userId2);
    }

    public function getConversations($userId)
    {
        return $this->messageRepo->getConversations($userId);
    }

    public function deleteMessage($messageId)
    {
        return $this->messageRepo->delete($messageId);
    }

    public function updateMessage($messageId, $message)
    {
        return $this->messageRepo->update($messageId, ['message' => $message]);
    }

    public function followUser($followerId, $followingId)
    {
        return $this->followRepo->create([
            'follower_id' => $followerId,
            'following_id' => $followingId
        ]);
       
        
        
    }

    public function unfollowUser($followerId, $followingId): bool   
    {
        $follow = $this->followRepo->getFollowing($followerId)->where('following_id', $followingId)->first();
        if ($follow) {
            return $this->followRepo->delete($follow->id);
        }
        return false;
    }

    public function getFollowers($userId)
    {
        return $this->followRepo->getFollowers($userId);
    }

    public function getFollowing($userId)
    {
        return $this->followRepo->getFollowing($userId);
    }

    public function countFollowers($userId)
    {
        return $this->followRepo->countFollowers($userId);
    }

    public function countFollowing($userId)
    {
        return $this->followRepo->countFollowing($userId);
    }

    public function isFollowing($followerId, $followingId)
    {
        return $this->followRepo->isFollowing($followerId, $followingId);
    }

   

}