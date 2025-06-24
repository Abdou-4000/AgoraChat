<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = ['name', 'type', 'created_by'];
    
    public function messages() { return $this->hasMany(Message::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
