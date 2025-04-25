<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OnboardingTask;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            // Altri seeder...
        ]);

        // Create sample onboarding tasks
        $this->createOnboardingTasks();
    }

    /**
     * Create sample onboarding tasks
     */
    private function createOnboardingTasks()
    {
        $tasks = [
            [
                'title' => 'Complete personal information',
                'description' => 'Update your profile with all personal information',
                'category' => 'Personal',
                'priority' => 'high',
                'deadline_days' => 3,
                'is_required' => true,
            ],
            [
                'title' => 'IT equipment setup',
                'description' => 'Set up your computer, email and all necessary software',
                'category' => 'IT',
                'priority' => 'high',
                'deadline_days' => 1,
                'is_required' => true,
            ],
            [
                'title' => 'Review company handbook',
                'description' => 'Read the company policies and handbook',
                'category' => 'HR',
                'priority' => 'medium',
                'deadline_days' => 5,
                'is_required' => true,
            ],
            [
                'title' => 'Schedule welcome meeting',
                'description' => 'Schedule a meeting with your team',
                'category' => 'Teams',
                'priority' => 'medium',
                'deadline_days' => 7,
                'is_required' => false,
            ],
            [
                'title' => 'Complete required training',
                'description' => 'Complete all assigned training courses',
                'category' => 'Training',
                'priority' => 'high',
                'deadline_days' => 14,
                'is_required' => true,
            ],
        ];

        foreach ($tasks as $task) {
            OnboardingTask::updateOrCreate(
                ['title' => $task['title']],
                $task
            );
        }
    }
}
