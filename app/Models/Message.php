<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Add user_id to the fillable array
    protected $fillable = ['user_id', 'chat_room_id', 'content'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }
}