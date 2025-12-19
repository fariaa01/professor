<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingLeft implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;
    public $userId;
    public $userType;

    public function __construct($meetingId, $userId, $userType)
    {
        $this->meetingId = $meetingId;
        $this->userId = $userId;
        $this->userType = $userType;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('meeting.' . $this->meetingId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_type' => $this->userType,
            'timestamp' => now()->toISOString(),
        ];
    }
}
