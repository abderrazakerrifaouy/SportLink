<?php
namespace App\Repositories;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Reaction;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository {
    public function getAllPosts() {
        return Post::with(['user', 'media', 'reactions'])->latest()->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['user', 'media', 'reactions'])->latest()->paginate($perPage);
    }

    public function searchByContentWithPagination(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['user', 'media', 'reactions'])
            ->where('content', 'like', '%' . $query . '%')
            ->latest()
            ->paginate($perPage);
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

    public function findPostForMutation($id)
    {
        return Post::find($id);
    }
    public function deletePost($id) {
        $post = Post::find($id);
        if ($post) {
            $commentIds = Comment::where('post_id', $post->id)->pluck('id');
            $replyIds = Reply::whereIn('comment_id', $commentIds)->pluck('id');

            Reaction::where('reactable_type', Post::class)
                ->where('reactable_id', $post->id)
                ->orWhere(function ($query) use ($post) {
                    $query->where('reactable_type', 'Post')->where('reactable_id', $post->id);
                })
                ->orWhere(function ($query) use ($commentIds) {
                    $query->whereIn('reactable_type', [Comment::class, 'Comment'])->whereIn('reactable_id', $commentIds);
                })
                ->orWhere(function ($query) use ($replyIds) {
                    $query->whereIn('reactable_type', [Reply::class, 'Reply', 'App\\Models\\Reply'])->whereIn('reactable_id', $replyIds);
                })
                ->delete();

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
