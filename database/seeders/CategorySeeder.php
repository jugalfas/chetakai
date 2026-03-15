<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Business',
            'Marketing',
            'AI',
            'Finance',
            'Self Improvement',
            'Fitness',
            'Travel',
            'Fashion',
            'Relationships',
            'Technology',
            'Productivity',
            'Health',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category],
                ['user_id' => null]
            );
        }
    }
}
