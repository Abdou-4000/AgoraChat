<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;

// Private channel for user-specific messages
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channel for chat rooms
Broadcast::channel('chat-room.{roomId}', function ($user, $roomId) {
    return true;
});