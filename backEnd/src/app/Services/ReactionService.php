<?php

namespace App\Services;


use App\Repositories\ReactionRepository;


class ReactionService
{
    protected $reactionRepository;

    public function __construct(ReactionRepository $reactionRepository)
    {
        $this->reactionRepository = $reactionRepository;
    }

    public function createReaction($type, $userId, $reactableId, $reactableType)
    {
        return $this->reactionRepository->createReaction($type, $userId, $reactableId, $reactableType);
    }

    public function deleteReaction($id)
    {
        return $this->reactionRepository->deleteReaction($id);
    }
}
