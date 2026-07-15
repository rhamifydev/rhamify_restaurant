<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $avatar;
    public $receiverId;
    public $senderId;
    public $senderName;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $avatar, $receiverId, $senderId, $senderName)
    {
        $this->message = $message;
        $this->avatar = $avatar;
        $this->receiverId = $receiverId;
        $this->senderId = $senderId;
        $this->senderName = $senderName;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.'.$this->receiverId),
        ];
    }
}
