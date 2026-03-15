<?php

namespace Database\Seeders;

use App\Models\ContentGoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentGoalSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
            'Inspire',
            'Educate',
            'Promote',
            'Entertain',
            'Storytelling',
            'Drive Traffic',
            'Increase Engagement',
        ];

        foreach ($goals as $goal) {
            ContentGoal::updateOrCreate(
                ['slug' => Str::slug($goal)],
                [
                    'name' => $goal,
                    'status' => true,
                ]
            );
        }
    }
}
