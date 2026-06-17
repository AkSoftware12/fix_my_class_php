<?php

namespace App\Services;

use App\Models\ChatRoom;
use App\Models\Message;

class ChatModerationService
{
    public function toggleRoomStatus(ChatRoom $room): ChatRoom
    {
        $room->is_active = ! $room->is_active;
        $room->save();

        return $room;
    }

    public function flagMessage(Message $message): Message
    {
        $message->is_flagged = ! $message->is_flagged;
        $message->save();

        return $message;
    }

    public function removeMessage(Message $message): Message
    {
        $message->update(['deleted_by_admin_at' => now(), 'body' => null, 'attachment_path' => null]);

        return $message;
    }
}
