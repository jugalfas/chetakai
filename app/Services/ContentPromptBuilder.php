<?php

namespace App\Services;

class ContentPromptBuilder
{
    public function build(array $settings): string
    {
        $parts = [];
        if (!empty($settings['category'])) $parts[] = 'Category: '.$settings['category'];
        if (!empty($settings['content_type'])) $parts[] = 'Content Type: '.$settings['content_type'];
        if (!empty($settings['tone'])) $parts[] = 'Tone: '.$settings['tone'];
        if (!empty($settings['audience'])) $parts[] = 'Audience: '.$settings['audience'];
        if (!empty($settings['goal'])) $parts[] = 'Goal: '.$settings['goal'];
        if (!empty($settings['style'])) $parts[] = 'Style: '.$settings['style'];
        if (!empty($settings['length'])) $parts[] = 'Length: '.$settings['length'];

        $rules = [
            'Use simple language',
            'Avoid clichés',
            'Make content engaging',
            'Ensure originality',
        ];

        $specific = $this->typeSpecificRules($settings);
        if (!empty($specific)) $rules = array_merge($rules, $specific);

        $prompt = implode("\n", $parts)."\nRules:\n- ".implode("\n- ", $rules);
        return $prompt;
    }

    public function preview(array $settings): string
    {
        $type = $settings['content_type'] ?? 'text_post';
        switch ($type) {
            case 'quote_post':
                return 'Short, original quote with emotional impact and clear language.';
            case 'carousel_post':
                $slides = (int)($settings['carousel_slides'] ?? 5);
                $lines = ['Slide 1: Hook'];
                for ($i = 2; $i < $slides; $i++) $lines[] = 'Slide '.$i.': Educational point';
                $lines[] = 'Slide '.$slides.': Conclusion or CTA';
                return implode("\n", $lines);
            case 'reel_script':
                return "Hook (3s)\nMain message\nCall to action";
            case 'ai_image':
                return 'Descriptive image prompt based on subject, style, mood, lighting.';
            case 'meme':
                return "Top text\nBottom text";
            case 'caption_hashtags':
                return "Caption text\n#hashtag1 #hashtag2 ...";
            case 'content_ideas':
                return "10 short content ideas";
            default:
                return 'Short post content';
        }
    }

    protected function typeSpecificRules(array $settings): array
    {
        $type = $settings['content_type'] ?? null;
        switch ($type) {
            case 'quote_post':
                return [
                    'Short impactful sentences',
                    'Simple English',
                    'No clichés',
                    'Emotional or motivational',
                ];
            case 'carousel_post':
                return [
                    'Provide slide-structured output',
                ];
            case 'reel_script':
                return [
                    'Start with a 3-second hook',
                    'Include main message and CTA',
                ];
            case 'ai_image':
                return [
                    'Generate a detailed image prompt',
                ];
            case 'meme':
                return [
                    'Output top text and bottom text',
                ];
            case 'caption_hashtags':
                return [
                    'Include engaging caption and 10–20 relevant hashtags',
                ];
            case 'content_ideas':
                return [
                    'Generate 10 concise viral ideas',
                ];
            default:
                return [];
        }
    }
}

