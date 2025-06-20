<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $rooms = ChatRoom::with('creator')->latest()->take(10)->get();
        return view('chat.index', compact('rooms'));
    }
    
    public function show(ChatRoom $room)
    {
        if ($room->type === 'private' && !$room->users()->where('user_id', auth()->id())->exists()) {
            abort(403, 'Unauthorized access');
        }

        $messages = $room->messages()->with('user')->latest()->take(50)->get()->reverse();
        return view('chat.room', compact('room', 'messages'));
    }
    
    public function sendMessage(Request $request, ChatRoom $room)
    {
        $request->validate(['content' => 'required|string|max:500']);
        
        // Credit check for public rooms
        if ($room->type === 'public' && !auth()->user()->deductCredits(1)) {
            return back()->with('error', 'Not enough credits!');
        }
        
        $message = Message::create([
            'user_id' => auth()->id(),
            'chat_room_id' => $room->id,
            'content' => $request->content,
        ]);
        
        broadcast(new MessageSent($message->load('user')));
        
        return response()->json($message->load('user'));
    }
}

