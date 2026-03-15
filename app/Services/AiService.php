<?php

namespace App\Services;

use Exception;

class AiService
{
    public function generate(string $prompt): string
    {
        $openaiKey = config('services.openai.key');

        if (!$openaiKey) {
            // Fallback for local/dev environment when API key is not set.
            return "[FAKE AI RESPONSE] Generated from prompt: " . substr($prompt, 0, 220);
        }

        if (!class_exists(\OpenAI\OpenAI::class)) {
            throw new Exception('OpenAI client library not installed. Run composer require openai-php/client');
        }

        $client = \OpenAI\OpenAI::client($openaiKey);

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 800,
        ]);

        $content = data_get($response, 'choices.0.message.content');

        return is_string($content) ? trim($content) : '';
    }
}
