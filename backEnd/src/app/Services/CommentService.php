<?php

namespace App\Services;

use App\Repositories\CommentRepository;


class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function addComment($postId, $userId, $content)
    {
        return $this->commentRepository->addComment($postId, $userId, $content);
    }

    public function addReply($commentId, $userId, $content)
    {
        return $this->commentRepository->addReply($commentId, $userId, $content);
    }

    public function deleteComment($id)
    {
        return $this->commentRepository->deleteComment($id);
    }

    public function deleteReply($id)
    {
        return $this->commentRepository->deleteReply($id);
    }

    public function getCommentsByPostId($postId)
    {
        return $this->commentRepository->getCommentsByPostId($postId);
    }

    public function getRepliesByCommentId($commentId)
    {
        return $this->commentRepository->getRepliesByCommentId($commentId);
    }
}
