<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chat_room_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
            $table->index(['chat_room_id', 'created_at'], 'messages_room_time_idx');
            $table->index(['user_id', 'created_at'], 'messages_user_time_idx');
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};