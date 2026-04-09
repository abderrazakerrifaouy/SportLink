<?php
namespace App\Repositories;

use App\Models\Comment;
use App\Models\Reply;

class CommentRepository {
    public function addComment($postId, $userId, $content) {
        return Comment::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);
    }

    public function addReply($commentId, $userId, $content) {
        return Reply::create([
            'comment_id' => $commentId,
            'user_id' => $userId,
            'content' => $content
        ]);
    }

    public function deleteComment($id) {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->delete();
            return true;
        }
        return false;
    }

    public function deleteReply($id) {
        $reply = Reply::find($id);
        if ($reply) {
            $reply->delete();
            return true;
        }
        return false;
    }
    public function getCommentsByPostId($postId) {
        return Comment::where('post_id', $postId)->with(['user', 'reactions', 'replies.user'])->latest()->get();
    }

    public function getRepliesByCommentId($commentId) {
        return Reply::where('comment_id', $commentId)->with(['user', 'reactions'])->latest()->get();
    }
}
