<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCOffer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;
    public $offer;
    public $from;

    public function __construct($meetingId, $offer, $from)
    {
        $this->meetingId = $meetingId;
        $this->offer = $offer;
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
            'offer' => $this->offer,
            'from' => $this->from,
        ];
    }
}
