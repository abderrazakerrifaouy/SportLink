<?php
namespace App\Repositories;

use App\Models\Post;

class PostRepository {
    public function getAllPosts() {
        return Post::with(['user', 'media', 'reactions'])->latest()->get();
    }

    public function createPost(array $data, $userId) {
        return Post::create([
            'content' => $data['content'],
            'user_id' => $userId
        ]);
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
    public function updatePost($id, array $data) {
        $post = Post::find($id);
        if ($post) {
            $post->update($data);
            return $post;
        }
        return null;
    }
}
