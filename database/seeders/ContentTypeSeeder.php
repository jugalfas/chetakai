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
            'Text Post',
            'Quote Post',
            'Carousel Post',
            'Reel Script',
            'AI Image',
            'Meme',
            'Story Post',
            'Caption + Hashtags',
            'Content Ideas',
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
