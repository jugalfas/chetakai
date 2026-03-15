<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\GeneratedContent;
use App\Services\ContentPromptBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenerationController extends Controller
{
    public function generate(Request $request, ContentPromptBuilder $builder)
    {
        $validated = $request->validate([
            'content_type' => 'required|string|in:text_post,quote_post,carousel_post,reel_script,ai_image,meme,story_post,caption_hashtags,content_ideas',
            'category' => 'nullable|string|max:255',
            'goal' => 'nullable|string|max:255',
            'tone' => 'nullable|string|max:255',
            'audience' => 'nullable|string|max:255',
            'length' => 'nullable|string|max:255',
            'style' => 'nullable|string|max:255',
            'bulk' => 'nullable|integer|min:1|max:20',
            'options' => 'array',
        ]);

        $count = (int)($validated['bulk'] ?? 1);
        $results = [];
        $prompt = $builder->build(array_merge($validated, $validated['options'] ?? []));

        for ($i = 0; $i < $count; $i++) {
            $preview = $builder->preview(array_merge($validated, $validated['options'] ?? []));
            $saved = GeneratedContent::create([
                'user_id' => Auth::id(),
                'content_type' => $validated['content_type'],
                'category' => $validated['category'] ?? null,
                'prompt' => $prompt,
                'result' => $preview,
                'settings_json' => array_merge($validated, $validated['options'] ?? []),
            ]);
            $results[] = [
                'id' => $saved->id,
                'result' => $saved->result,
            ];
        }

        return response()->json([
            'prompt' => $prompt,
            'results' => $results,
        ]);
    }
}

