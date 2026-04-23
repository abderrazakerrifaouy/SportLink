<?php

namespace App\Services;

use App\Models\Media;
use App\Repositories\PostRepository;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts()
    {
        return $this->postRepository->getAllPosts();
    }

    public function createPost($content, $userId, array $media = [])
    {
        $post = $this->postRepository->createPost($content, $userId);

        if (!empty($media)) {
            foreach ($media as $mediaItem) {
                Media::create([
                    'url' => $mediaItem['url'],
                    'mediaType' => $mediaItem['mediaType'], // default
                    'post_id' => $post->id,
                ]);
            }
        }

        $post->load(['media', 'reactions.user', 'comments.user', 'comments.replies.user'])->load([
            'reactions' => function ($query) {
                $query->select('reactable_id', 'type')->selectRaw('COUNT(*) as total')->groupBy('reactable_id', 'type');
            },
        ]);
        return $post;
    }

    public function findPost($id)
    {
        return $this->postRepository->findPost($id)->load(['media', 'reactions.user', 'comments.user', 'comments.replies.user'])->load([
            'reactions' => function ($query) {
                $query->select('reactable_id', 'type')->selectRaw('COUNT(*) as total')->groupBy('reactable_id', 'type');
            },
        ]);
    }

    public function deletePost($id)
    {
        return $this->postRepository->deletePost($id);
    }

    public function updatePost($id, $userId, $content, array $media = [])
    {
        $post = $this->postRepository->findPostForMutation($id);

        if (!$post) {
            throw new \DomainException('Post not found.');
        }

        if ((int) $post->user_id !== (int) $userId) {
            throw new \DomainException('You are not allowed to update this post.');
        }

        if (!empty($media)) {
            $post = $this->postRepository->updatePost($id, $content);
            if ($post) {
                $post->media()->delete();
                foreach ($media as $mediaItem) {
                    Media::create(['url' => $mediaItem['url'], 'mediaType' => $mediaItem['mediaType'], 'post_id' => $id]);
                }
            }
            return $this->postRepository->findPost($id);
        }
        return $this->postRepository->updatePost($id, $content);
    }

    public function deletePostByUser($id, $userId)
    {
        $post = $this->postRepository->findPostForMutation($id);

        if (!$post) {
            throw new \DomainException('Post not found.');
        }

        if ((int) $post->user_id !== (int) $userId) {
            throw new \DomainException('You are not allowed to delete this post.');
        }

        return $this->postRepository->deletePost($id);
    }

    public function getPostsByUserId($userId)
    {
        return $this->postRepository->getPostsByUserId($userId);
    }
}
