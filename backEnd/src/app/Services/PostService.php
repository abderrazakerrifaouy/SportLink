<?php

namespace App\Services;

use App\Repositories\PostRepository;

class PostService {
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts()
    {
        return $this->postRepository->getAllPosts();
    }

    public function createPost($data, $userId)
    {
        return $this->postRepository->createPost($data, $userId);
    }

    public function findPost($id)
    {
        return $this->postRepository->findPost($id);
    }
    

}
