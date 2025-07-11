<?php

namespace App\Events;

use App\Models\DirectMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DirectMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public DirectMessage $message)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->message->receiver_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {

        Log::debug('Broadcasting message', [
            'to' => 'user.' . $this->message->receiver_id,
            'data' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender->name,
                'created_at' => $this->message->created_at->format('M j, g:i A'),
            ]
        ]);

        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'created_at' => $this->message->created_at->format('M j, g:i A'),
        ];
        

    }
    public function broadcastAs()
    {
        return 'DirectMessageSent';
    }
}