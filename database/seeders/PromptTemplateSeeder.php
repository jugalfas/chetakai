<?php

namespace Database\Seeders;

use App\Models\PromptTemplate;
use Illuminate\Database\Seeder;

class PromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $promptTemplate = <<<'TEXT'

Generate a high quality {{content_type}} for {{platform}}.

Category: {{category}}
Goal: {{goal}}
Tone: {{tone}}
Audience: {{audience}}
Writing Style: {{style}}

Image Details:
Subject: {{image_subject}}
Style: {{image_style}}
Mood: {{image_mood}}
Lighting: {{image_lighting}}

Make it visually appealing and optimized for social media engagement.

TEXT;

        PromptTemplate::updateOrCreate(
            [
                'platform_id' => 1,
                'content_type_id' => 5,
            ],
            [
                'name' => 'Instagram AI Image Generator',
                'prompt_template' => trim($promptTemplate),
                'status' => true,
            ]
        );
    }
}
