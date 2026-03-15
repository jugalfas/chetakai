<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Services\PromptBuilder;
use Illuminate\Http\JsonResponse;

class PromptGenerationController extends Controller
{
    public function __construct(
        protected PromptBuilder $promptBuilder
    ) {}

    /**
     * Generate a prompt from a template (no AI generation, prompt only).
     */
    public function generateFromTemplate(Template $template): JsonResponse
    {
        $template->load([
            'platform',
            'contentType',
            'category',
            'contentGoal',
            'tone',
            'audience',
            'style',
            'templateAttributes.attribute',
        ]);

        $prompt = $this->promptBuilder->buildFromTemplate($template);

        return response()->json([
            'template_id' => $template->id,
            'prompt'      => $prompt,
        ]);
    }
}
