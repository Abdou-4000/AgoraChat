<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debate_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('vote', ['a', 'b']);
            $table->timestamps();
            
            // Prevent duplicate votes
            $table->unique(['debate_id', 'user_id'], 'unique_user_debate_vote');
            
            // Index for vote counting
            $table->index(['debate_id', 'vote'], 'debate_vote_count_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debate_votes');
    }
};