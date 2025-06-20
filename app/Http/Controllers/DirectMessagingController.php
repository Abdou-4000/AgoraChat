<?php

namespace App\Http\Controllers;

use App\Events\DirectMessageSent;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectMessagingController extends Controller
{
    public function index()
    {
        // Get users with whom the current user has conversations
        $conversationUsers = User::whereIn('id', function($query) {
            $query->select('sender_id')
                ->from('direct_messages')
                ->where('receiver_id', auth()->id())
                ->union(
                    DB::table('direct_messages')
                    ->select('receiver_id')
                    ->where('sender_id', auth()->id())
                );
        })->get();
        
        // Get unread message counts
        $unreadCounts = DirectMessage::where('receiver_id', auth()->id())
            ->where('read', false)
            ->select('sender_id', DB::raw('count(*) as count'))
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');
        
        return view('messages.index', compact('conversationUsers', 'unreadCounts'));
    }
    
    public function sendMessage(Request $request, $receiverId)
    {
        $request->validate([
            'content' => 'required|string|max:500'
        ]);
        
        // Create the message
        $message = DirectMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiverId,
            'content' => $request->content,
        ]);
        
        // Load the sender relationship for broadcasting
        $message->load('sender');
        
        // Broadcast to the receiver
        broadcast(new DirectMessageSent($message));
        
        return response()->json($message);
    }
    
    public function showConversation($userId)
    {
        // Get the other user
        $otherUser = User::findOrFail($userId);
        
        // Get messages between current user and the other user
        $messages = DirectMessage::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })
        ->with('sender')
        ->orderBy('created_at')
        ->get();
        
        // Mark unread messages as read
        DirectMessage::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);
        
        return view('messages.show', compact('messages', 'otherUser'));
    }
    
    public function getLatestMessages($userId)
    {
        // For AJAX polling or initial load in SPA
        $messages = DirectMessage::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })
        ->with('sender')
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get()
        ->reverse();
        
        return response()->json($messages);
    }
}