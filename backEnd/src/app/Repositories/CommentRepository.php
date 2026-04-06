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
}
