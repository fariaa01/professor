<?php

namespace App\Events;

use App\Models\MeetingMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingMessage;

    public function __construct(MeetingMessage $meetingMessage)
    {
        $this->meetingMessage = $meetingMessage;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('meeting.' . $this->meetingMessage->meeting_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->meetingMessage->id,
            'message' => $this->meetingMessage->message,
            'sender_type' => $this->meetingMessage->sender_type,
            'sender_id' => $this->meetingMessage->sender_id,
            'sender_name' => $this->meetingMessage->sender_name,
            'is_system_message' => $this->meetingMessage->is_system_message,
            'created_at' => $this->meetingMessage->created_at->toISOString(),
            'formatted_time' => $this->meetingMessage->formatted_time,
        ];
    }
}
