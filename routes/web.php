<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DebateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DirectMessagingController;


Route::get('/', function () {
    return redirect()->route('chat.index');
});

Route::get('/dashboard', function () {
    return redirect()->route('chat.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // AgoraChat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{room}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{room}/message', [ChatController::class, 'sendMessage'])->name('chat.message');
    
    Route::get('/debates', [DebateController::class, 'index'])->name('debates.index');
    Route::post('/debates/{debate}/vote', [DebateController::class, 'vote'])->name('debates.vote');

    Route::get('/messages', [DirectMessagingController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [DirectMessagingController::class, 'showConversation'])->name('messages.conversation');
    Route::post('/messages/{user}', [DirectMessagingController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/{user}/latest', [DirectMessagingController::class, 'getLatestMessages'])->name('messages.latest');

});

require __DIR__.'/auth.php';