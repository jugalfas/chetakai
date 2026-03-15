<?php

namespace Database\Seeders;

use App\Models\Audience;
use Illuminate\Database\Seeder;

class AudienceSeeder extends Seeder
{
    public function run(): void
    {
        $audiences = [
            'Everyone',
            'Entrepreneurs',
            'Students',
            'Creators',
            'Business Owners',
            'Gym Beginners',
            'Small Business Owners',
            'Freelancers',
        ];

        foreach ($audiences as $audience) {
            Audience::updateOrCreate(
                ['name' => $audience],
                [
                    'user_id' => null,
                    'is_default' => false,
                    'status' => true,
                ]
            );
        }
    }
}
