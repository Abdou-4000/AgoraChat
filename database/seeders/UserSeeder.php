<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'tier' => 'elite',
            'credits' => 500,
        ]);

        // Create multiple Elite tier users (200+ credits)
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Elite User {$i}",
                'email' => "elite{$i}@example.com",
                'password' => Hash::make('password'),
                'tier' => 'elite',
                'credits' => rand(200, 350),
            ]);
        }

        // Create multiple Contributor tier users (50-199 credits)
        for ($i = 1; $i <= 8; $i++) {
            User::create([
                'name' => "Contributor User {$i}",
                'email' => "contributor{$i}@example.com",
                'password' => Hash::make('password'),
                'tier' => 'contributor',
                'credits' => rand(50, 199),
            ]);
        }

        // Create multiple Discovery tier users (0-49 credits)
        for ($i = 1; $i <= 12; $i++) {
            User::create([
                'name' => "Discovery User {$i}",
                'email' => "discovery{$i}@example.com",
                'password' => Hash::make('password'),
                'tier' => 'discovery',
                'credits' => rand(0, 49),
            ]);
        }
    }
}