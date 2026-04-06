<?php
namespace App\Repositories;

use App\Models\Reaction;

class ReactionRepository {
    public function toggleReaction($user, $model, $type) {
        // model hna t9der tkon Post awla Comment
        $reaction = $model->reactions()->where('user_id', $user->id)->first();

        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete();
                return ['status' => 'removed'];
            }
            $reaction->update(['type' => $type]);
            return ['status' => 'updated'];
        }

        $model->reactions()->create([
            'user_id' => $user->id,
            'type' => $type
        ]);

        return ['status' => 'added'];
    }
}
