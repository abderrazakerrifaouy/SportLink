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

    public function updatePost($id, $content, array $media = [])
    {
        if (!empty($media)) {
            $post = $this->postRepository->updatePost($id, $content);
            if ($post) {
                $post->media()->delete();
                foreach ($media as $mediaItem) {
                    Media::create(['url' => $mediaItem['url'], 'mediaType' => $mediaItem['mediaType'], 'post_id' => $id]);
                }
            }
            return $post;
        }
        return $this->postRepository->updatePost($id, $content);
    }

    public function getPostsByUserId($userId)
    {
        return $this->postRepository->getPostsByUserId($userId);
    }
}
