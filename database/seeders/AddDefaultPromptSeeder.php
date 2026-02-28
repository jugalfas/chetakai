<?php

namespace Database\Seeders;

use App\Models\Prompt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddDefaultPromptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prompt = 'Avoid generating quotes similar to ANY of these previous quotes:

            [quotes]

            Your quote must be clearly different in wording, structure, and emotional message.
            If anything feels even slightly similar, rewrite it.

            Goal: Create a motivational quote that feels personal, warm, and human — like something a real person would say to a friend who is struggling.

            Language rules (very important):
            - Use very simple, everyday English
            - Short sentences preferred
            - No complex vocabulary
            - No poetic or philosophical jargon
            - No rare or fancy words
            - The reader should understand it instantly without needing a dictionary
            - The quote should focus on feelings, hope, healing, growth, kindness, or self-belief
            - Make it emotional and relatable, not corporate or preachy

            Avoid:
            - Clichés
            - Overused motivation lines
            - Famous quote styles
            - Metaphors that are hard to understand
            - “success”, “grind”, “hustle”, “millionaire mindset” type motivation

            Generate 1 original motivational quote.

            For EACH quote:
            - Generate the quote text
            - Generate a warm Instagram-style caption that feels natural and comforting
            - Generate 8-12 relevant hashtags

            FORMAT RULE (important):
            Caption text first, then a blank line, then hashtags.

            Example:
            This is the caption text.

            #hashtag1 #hashtag2 #hashtag3

            Do not add explanations.
            Do not add markdown.
            Return only the quote and the combined caption + hashtags in the required structured format.
        ';

        Prompt::create([
            'prompt' => $prompt,
            'type' => 'post',
            'user_id' => null,
            'created_at' => now(),
        ]);
        Prompt::create([
            'prompt' => $prompt,
            'type' => 'reel',
            'user_id' => null,
            'created_at' => now(),
        ]);
    }
}
