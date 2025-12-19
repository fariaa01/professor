<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCAnswer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;
    public $answer;
    public $from;

    public function __construct($meetingId, $answer, $from)
    {
        $this->meetingId = $meetingId;
        $this->answer = $answer;
        $this->from = $from;
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
            'answer' => $this->answer,
            'from' => $this->from,
        ];
    }
}
