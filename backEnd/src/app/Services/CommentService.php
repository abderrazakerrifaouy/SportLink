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

    public function updateComment($id, $userId, $content)
    {
        $comment = $this->commentRepository->findCommentById($id);

        if (!$comment) {
            throw new \DomainException('Comment not found.');
        }

        if ((int) $comment->user_id !== (int) $userId) {
            throw new \DomainException('You are not allowed to update this comment.');
        }

        return $this->commentRepository->updateComment($comment, $content);
    }

    public function updateReply($id, $userId, $content)
    {
        $reply = $this->commentRepository->findReplyById($id);

        if (!$reply) {
            throw new \DomainException('Reply not found.');
        }

        if ((int) $reply->user_id !== (int) $userId) {
            throw new \DomainException('You are not allowed to update this reply.');
        }

        return $this->commentRepository->updateReply($reply, $content);
    }

    public function deleteComment($id, $userId)
    {
        $comment = $this->commentRepository->findCommentById($id);

        if (!$comment) {
            throw new \DomainException('Comment not found.');
        }

        if ((int) $comment->user_id !== (int) $userId) {
            throw new \DomainException('You are not allowed to delete this comment.');
        }

        return $this->commentRepository->deleteComment($comment);
    }

    public function deleteReply($id, $userId)
    {
        $reply = $this->commentRepository->findReplyById($id);

        if (!$reply) {
            throw new \DomainException('Reply not found.');
        }

        if ((int) $reply->user_id !== (int) $userId) {
            throw new \DomainException('You are not allowed to delete this reply.');
        }

        return $this->commentRepository->deleteReply($reply);
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
