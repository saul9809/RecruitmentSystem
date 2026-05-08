<?php

namespace Database\Seeders;

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

        $this->call([
            UserSeeder::class,
            DepartmentsSeeder::class,
            SkillsSeeder::class,
            ProfilesSeeder::class,
            ProfileRequirementsSeeder::class,
            CandidatesSeeder::class,
            CandidateEducationSeeder::class,
            CandidateExperienceSeeder::class,
            CandidateSkillsSeeder::class,
            CandidateStageHistorySeeder::class,
            CandidateProfileScoreSeeder::class,
            CvSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
