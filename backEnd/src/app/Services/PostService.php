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

    public function deletePost($id)
    {
        return $this->postRepository->deletePost($id);
    }

    public function updatePost($id, $data)
    {
        return $this->postRepository->updatePost($id, $data);
    }

    public function getPostsByUserId($userId)
    {
        return $this->postRepository->getPostsByUserId($userId);
    }
}
