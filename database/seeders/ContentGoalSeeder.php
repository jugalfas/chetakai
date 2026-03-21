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
            'Growth',
            'Engagement',
            'Education',
            'Inspiration',
            'Promotion',
            'Community',
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
