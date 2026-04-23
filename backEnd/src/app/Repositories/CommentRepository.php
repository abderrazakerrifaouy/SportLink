<?php
namespace App\Repositories;

use App\Models\Comment;
use App\Models\Reply;

class CommentRepository {
    public function findCommentById($id)
    {
        return Comment::with(['user', 'replies.user', 'replies.reactions', 'reactions'])->find($id);
    }

    public function findReplyById($id)
    {
        return Reply::with(['user', 'reactions'])->find($id);
    }

    public function addComment($postId, $userId, $content) {
        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);

        return $comment->load(['user', 'replies.user']);
    }

    public function addReply($commentId, $userId, $content) {
        $reply = Reply::create([
            'comment_id' => $commentId,
            'user_id' => $userId,
            'content' => $content
        ]);

        return $reply->load(['user', 'reactions']);
    }

    public function updateComment(Comment $comment, string $content): Comment
    {
        $comment->update([
            'content' => $content,
        ]);

        return $comment->refresh()->load(['user', 'replies.user', 'replies.reactions', 'reactions']);
    }

    public function updateReply(Reply $reply, string $content): Reply
    {
        $reply->update([
            'content' => $content,
        ]);

        return $reply->refresh()->load(['user', 'reactions']);
    }

    public function deleteComment(Comment $comment) {
        return $comment->delete();
    }

    public function deleteReply(Reply $reply) {
        return $reply->delete();
    }
    public function getCommentsByPostId($postId) {
        return Comment::where('post_id', $postId)
            ->with(['user', 'reactions', 'replies.user', 'replies.reactions'])
            ->latest()
            ->get();
    }

    public function getRepliesByCommentId($commentId) {
        return Reply::where('comment_id', $commentId)->with(['user', 'reactions'])->latest()->get();
    }
}
