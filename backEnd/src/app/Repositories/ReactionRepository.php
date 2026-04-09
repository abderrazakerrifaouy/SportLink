<?php
namespace App\Repositories;

use App\Models\Reaction;

class ReactionRepository {
    public function createReaction($type, $userId, $reactableId, $reactableType)
    {
        if (Reaction::where('user_id', $userId)
            ->where('reactable_id', $reactableId)
            ->where('reactable_type', $reactableType)
            ->exists()) {
            throw new \InvalidArgumentException("User has already reacted to this item.");
        }
        return Reaction::create([
            'type' => $type,
            'user_id' => $userId,
            'reactable_id' => $reactableId,
            'reactable_type' => $reactableType,
        ]);
    }
    public function deleteReaction($id)
    {
        $reaction = Reaction::find($id);
        if ($reaction) {
            $reaction->delete();
            return true;
        }
        return false;
    }
}
