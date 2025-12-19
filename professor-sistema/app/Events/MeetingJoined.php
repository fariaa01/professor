<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;
    public $userId;
    public $userName;
    public $userType; // 'professor' ou 'aluno'

    public function __construct($meetingId, $userId, $userName, $userType)
    {
        $this->meetingId = $meetingId;
        $this->userId = $userId;
        $this->userName = $userName;
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
            'user_name' => $this->userName,
            'user_type' => $this->userType,
            'timestamp' => now()->toISOString(),
        ];
    }
}
