<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->boolean('read')->default(false);
            $table->timestamps();
            
            // Indexes for fast lookups
            $table->index(['sender_id', 'receiver_id', 'created_at']);
            $table->index(['receiver_id', 'sender_id', 'created_at']);
            $table->index(['receiver_id', 'read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_messages');
    }
};