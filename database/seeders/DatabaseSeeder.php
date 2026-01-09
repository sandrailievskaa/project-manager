<?php

namespace Database\Seeders;

use App\Enums\UserExperience;
use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()
            ->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'role' => UserRole::ADMIN,
                'experience' => UserExperience::SENIOR,
                'password' => 'admin',
            ]);
    }
}
