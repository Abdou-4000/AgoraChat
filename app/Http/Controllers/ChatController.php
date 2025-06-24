<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Models\DirectMessage; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatRoom::with('creator')->latest();
        
        // Handle room type filtering
        if ($request->has('type') && in_array($request->type, ['public', 'elite', 'private'])) {
            $query->where('type', $request->type);
        }
        
        // Handle room search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $rooms = $query->take(10)->get();
        
        // Get unread message counts for direct messages (from original implementation)
        $unreadCounts = DirectMessage::where('receiver_id', auth()->id())
            ->where('read', false)
            ->select('sender_id', DB::raw('count(*) as count'))
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id')
            ->toArray();
        
        return view('chat.index', compact('rooms', 'unreadCounts'));
    }
    
    public function show(ChatRoom $room)
    {
        // Check permissions for all restricted room types
        if (($room->type === 'private' && !$room->users()->where('user_id', auth()->id())->exists()) ||
            ($room->type === 'elite' && auth()->user()->tier !== 'elite')) {
            
            return redirect()->route('chat.index')
                ->with('error', 'You don\'t have permission to access this room.');
        }
    
        $messages = $room->messages()->with('user')->latest()->take(50)->get()->reverse();
        
        // Get unread message counts (from original implementation)
        $unreadCounts = DirectMessage::where('receiver_id', auth()->id())
            ->where('read', false)
            ->select('sender_id', DB::raw('count(*) as count'))
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id')
            ->toArray();
        
        return view('chat.rooms', compact('room', 'messages', 'unreadCounts'));
    }
    
    public function sendMessage(Request $request, ChatRoom $room)
    {
        try {
            $request->validate(['content' => 'required|string|max:500']);
            
            // Credit check for public rooms
            if ($room->type === 'public') {
                $user = auth()->user();
                if ($user->credits < 1) {
                    return response()->json(['error' => 'Not enough credits!'], 403);
                }
                
                // Safely deduct credits
                if (method_exists($user, 'deductCredits')) {
                    $user->deductCredits(1);
                } else {
                    $user->credits -= 1;
                    $user->save();
                }
            }
            
            $message = Message::create([
                'user_id' => auth()->id(),
                'chat_room_id' => $room->id,
                'content' => $request->content,
            ]);
            
            // Load the user relationship for broadcasting
            $message->load('user');
            
            // Broadcast directly without queue for testing
            broadcast(new MessageSent($message))->toOthers();
            
            return response()->json([
                'id' => $message->id,
                'content' => $message->content,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'created_at' => $message->created_at->format('M j, g:i A'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending chat message: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while sending your message'], 500);
        }
    }
}

