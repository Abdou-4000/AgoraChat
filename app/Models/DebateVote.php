<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebateVote extends Model
{
    protected $fillable = ['debate_id', 'user_id', 'vote'];
    
    public function debate() { return $this->belongsTo(Debate::class); }
    public function user() { return $this->belongsTo(User::class); }
}
