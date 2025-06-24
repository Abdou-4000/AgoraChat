<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debate extends Model
{
    protected $fillable = ['title', 'side_a', 'side_b', 'status', 'created_by'];
    
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function votes() { return $this->hasMany(DebateVote::class); }
    
    public function getVoteCounts()
    {
        return [
            'a' => $this->votes()->where('vote', 'a')->count(),
            'b' => $this->votes()->where('vote', 'b')->count(),
        ];
    }
}