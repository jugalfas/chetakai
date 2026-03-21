<?php

namespace Database\Seeders;

use App\Models\ContentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Single Image Post',
            'Quote Post',
            'Carousel Post',
            'Reel Script',
            'Reel Post',
            'Story Post',
            'Caption Only',
        ];

        foreach ($types as $type) {
            ContentType::updateOrCreate(
                ['slug' => Str::slug($type)],
                [
                    'name' => $type,
                    'status' => true,
                ]
            );
        }
    }
}
