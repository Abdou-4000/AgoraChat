<?php

namespace Database\Seeders;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users by tier for creating appropriate rooms
        $eliteUsers = User::where('tier', 'elite')->get();
        $contributorUsers = User::where('tier', 'contributor')->get();
        
        if ($eliteUsers->isEmpty() || $contributorUsers->isEmpty()) {
            $this->command->info('No users found. Please run the UserSeeder first.');
            return;
        }
        
        // Create some public rooms
        for ($i = 1; $i <= 5; $i++) {
            ChatRoom::create([
                'name' => "Public Chat Room {$i}",
                'type' => 'public',
                'created_by' => $contributorUsers->random()->id,
            ]);
        }
        
        // Create some elite rooms
        for ($i = 1; $i <= 3; $i++) {
            ChatRoom::create([
                'name' => "Elite Chat Room {$i}",
                'type' => 'elite',
                'created_by' => $eliteUsers->random()->id,
            ]);
        }
    }
}