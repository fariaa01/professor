<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCIceCandidate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;
    public $candidate;
    public $from;

    public function __construct($meetingId, $candidate, $from)
    {
        $this->meetingId = $meetingId;
        $this->candidate = $candidate;
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
            'candidate' => $this->candidate,
            'from' => $this->from,
        ];
    }
}
