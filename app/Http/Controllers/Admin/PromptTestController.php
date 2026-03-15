<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\PromptBuilder;
use Illuminate\Http\JsonResponse;

class PromptTestController extends Controller
{
    public function __construct(
        protected PromptBuilder $promptBuilder
    ) {}

    /**
     * Generate a prompt from a studio template (admin testing).
     */
    public function generate(Template $template): JsonResponse
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
            'prompt' => $prompt,
        ]);
    }
}
