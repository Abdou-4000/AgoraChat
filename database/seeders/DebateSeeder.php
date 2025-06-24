<?php

namespace Database\Seeders;

use App\Models\Debate;
use App\Models\User;
use Illuminate\Database\Seeder;

class DebateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run the UserSeeder first.');
            return;
        }
        
        $debates = [
            [
                'title' => 'Should AI be regulated?',
                'side_a' => 'Yes, AI needs strong regulation',
                'side_b' => 'No, regulation would stifle innovation'
            ],
            [
                'title' => 'Is remote work better than office work?',
                'side_a' => 'Yes, remote work improves productivity',
                'side_b' => 'No, in-office collaboration is essential'
            ],
            [
                'title' => 'Are cryptocurrencies the future of finance?',
                'side_a' => 'Yes, they will revolutionize financial systems',
                'side_b' => 'No, traditional currencies will remain dominant'
            ],
            [
                'title' => 'Is social media beneficial for society?',
                'side_a' => 'Yes, it connects people and spreads knowledge',
                'side_b' => 'No, it creates division and harms mental health'
            ],
            [
                'title' => 'Should college education be free?',
                'side_a' => 'Yes, education is a right',
                'side_b' => 'No, it should remain an investment'
            ],
        ];
        
        foreach ($debates as $debate) {
            Debate::create([
                'title' => $debate['title'],
                'side_a' => $debate['side_a'],
                'side_b' => $debate['side_b'],
                'created_by' => $users->random()->id,
            ]);
        }
    }
}