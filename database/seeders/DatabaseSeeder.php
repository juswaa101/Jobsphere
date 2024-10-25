<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Employer Account
        User::query()->create([
            'name' => 'employer test',
            'email' => 'employer@jobsphere.com',
            'password' => 'password',
            'role' => 2,
        ]);

        // Create Job Seeker Account
        User::query()->create([
            'name' => 'jobseeker test',
            'email' => 'jobseeker@jobsphere.com',
            'password' => 'password',
            'role' => 1,
        ]);
    }
}
