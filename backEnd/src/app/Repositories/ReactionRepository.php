<?php
namespace App\Repositories;

use App\Models\Reaction;

class ReactionRepository {
    public function createReaction($type, $userId, $reactableId, $reactableType)
    {
        if ($reactableType === 'App\\Models\\Post') {
            $reactableType = 'Post';
        }

        if ($reactableType === 'App\\Models\\Comment') {
            $reactableType = 'Comment';
        }

        $existingReaction = Reaction::where('user_id', $userId)
            ->where('reactable_id', $reactableId)
            ->where('reactable_type', $reactableType)
            ->first();

        if (!$existingReaction) {
            return [
                'action' => 'created',
                'reaction' => Reaction::create([
                    'type' => $type,
                    'user_id' => $userId,
                    'reactable_id' => $reactableId,
                    'reactable_type' => $reactableType,
                ]),
            ];
        }

        if ($existingReaction->type === $type) {
            $existingReaction->delete();

            return [
                'action' => 'removed',
                'reaction' => null,
            ];
        }

        $existingReaction->type = $type;
        $existingReaction->save();

        return [
            'action' => 'updated',
            'reaction' => $existingReaction,
        ];
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
