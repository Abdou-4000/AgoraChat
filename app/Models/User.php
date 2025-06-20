<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'credits', 'tier'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships with eager loading optimization for cloud DB
    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function debates()
    {
        return $this->hasMany(Debate::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(DebateVote::class);
    }

    // Business logic methods
    public function canCreatePublicRoom(): bool
    {
        return $this->tier !== 'discovery';
    }

    public function deductCredits(int $amount): bool
    {
        if ($this->credits >= $amount) {
            // Use atomic decrement for cloud database consistency
            return $this->decrement('credits', $amount) > 0;
        }
        return false;
    }

    public function addCredits(int $amount): void
    {
        $this->increment('credits', $amount);
        $this->checkTierUpgrade();
    }

    private function checkTierUpgrade(): void
    {
        $currentTier = $this->tier;
        
        if ($currentTier === 'discovery' && $this->credits >= 50) {
            $this->update(['tier' => 'contributor']);
        } elseif ($currentTier === 'contributor' && $this->credits >= 200) {
            $this->update(['tier' => 'elite']);
        }
    }

    public function sentDirectMessages()
    {
        return $this->hasMany(DirectMessage::class, 'sender_id');
    }

    /**
     * Get the direct messages received by the user
     */
    public function receivedDirectMessages()
    {
        return $this->hasMany(DirectMessage::class, 'receiver_id');
    }
}