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
        // Create admin user
        User::create([
            'name'              => 'Admin User',
            'email'             => 'admin@library.test',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create librarian user
        User::create([
            'name'              => 'Librarian User',
            'email'             => 'librarian@library.test',
            'password'          => Hash::make('password'),
            'role'              => 'librarian',
            'email_verified_at' => now(),
        ]);

        // Create member users
        User::create([
            'name'              => 'John Doe',
            'email'             => 'john@example.test',
            'password'          => Hash::make('password'),
            'role'              => 'member',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Jane Smith',
            'email'             => 'jane@example.test',
            'password'          => Hash::make('password'),
            'role'              => 'member',
            'email_verified_at' => now(),
        ]);

        // Create additional test members
        User::factory()->count(5)->create();
    }
}
