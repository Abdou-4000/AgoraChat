<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) 
    {
        // Load the user relationship if not already loaded
        if (!$message->relationLoaded('user')) {
            $message->load('user');
        }
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat-room.' . $this->message->chat_room_id);
    }
    
    // Add this method to provide data for the broadcast
    public function broadcastWith(): array
    {
        Log::debug('Broadcasting chat message', [
            'room' => $this->message->chat_room_id,
            'from' => $this->message->user->name
        ]);
        
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user->name,
            'created_at' => $this->message->created_at->format('M j, g:i A'),
        ];
    }
}