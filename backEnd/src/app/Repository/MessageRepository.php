<?php

namespace App\Repository;

use App\Models\Message;
use App\Models\User;

class MessageRepository
{
    public function create($data)
    {
        return Message::create([
            'sender_id' => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'message' => $data['message'],
        ]);
    }

    public function update($id, $data)
    {
        $message = Message::find($id);
        if ($message) {
            $message->update($data);
            return $message;
        }
        return null;
    }

    public function delete($id)
    {
        $message = Message::find($id);
        if ($message) {
            $message->delete();
            return true;
        }
        return false;
    }

    public function getConversation($userId1, $userId2)
    {
        return Message::where(function ($query) use ($userId1, $userId2) {
            $query->whereIn('sender_id', [$userId1, $userId2])
                ->whereIn('receiver_id', [$userId1, $userId2]);
        })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getConversations($userId)
    {
        // جلب جميع الرسائل ديال user (مرسلة ومستقبلة)
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        // group حسب الparticipant الآخر
        $grouped = $messages->groupBy(function ($message) use ($userId) {
            return $message->sender_id == $userId
                ? $message->receiver_id
                : $message->sender_id;
        });

        // map لهيكلية مناسبة
        $conversations = $grouped->map(function ($messages, $otherUserId) use ($userId) {
            if (!is_numeric($otherUserId)) return null; // skip invalid

            $user = User::find($otherUserId);
            if (!$user) return null;

            return [
                'user' => $user,
                'messages' => $messages,
            ];
        })->filter() // remove null
            ->values();

        return $conversations;
    }
}
