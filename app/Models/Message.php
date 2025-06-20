<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //protected $fillable = ['user_id', 'chat_room_id', 'content'];
    protected $fillable = ['sender_id', 'receiver_id', 'content'];
    
    public function user() { return $this->belongsTo(User::class); }
    public function chatRoom() { return $this->belongsTo(ChatRoom::class); }
}
