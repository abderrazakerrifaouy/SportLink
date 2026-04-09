<?php
namespace App\Repositories;

use App\Models\Post;

class PostRepository {
    public function getAllPosts() {
        return Post::with(['user', 'media', 'reactions'])->latest()->get();
    }

    public function createPost($content, $userId) {
        $post = Post::create([
            'content' => $content,
            'user_id' => $userId
        ]);

        return $post->with(['user', 'media', 'comments.user', 'comments.replies.user'])->find($post->id);
    }

    public function findPost($id) {
        return Post::with(['user', 'media', 'comments.user', 'comments.replies.user'])->findOrFail($id);
    }
    public function deletePost($id) {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return true;
        }
        return false;
    }
    public function updatePost($id, $content) {
        $post = Post::find($id);
        if ($post) {
            $post->update(['content' => $content]);
            return $post->with(['user', 'media', 'comments.user', 'comments.replies.user'])->find($id);
        }
        return null;
    }

    public function getPostsByUserId($userId) {
        return Post::where('user_id', $userId)->with(['user', 'media', 'reactions'])->latest()->get();
    }
}
