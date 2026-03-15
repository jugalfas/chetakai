<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedContent;
use App\Models\Template;
use App\Services\AiService;
use App\Services\PromptBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContentStudioController extends Controller
{
    public function index()
    {
        $templates = Template::query()
            ->with(['platform', 'contentType', 'category', 'contentGoal', 'tone', 'audience', 'style', 'templateAttributes.attribute'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/ContentStudio/Index', [
            'templates' => $templates,
        ]);
    }

    public function preview(Request $request, PromptBuilder $promptBuilder)
    {
        $validated = $request->validate([
            'template_id' => ['required', 'integer', 'exists:templates,id'],
        ]);

        $template = Template::with(['platform', 'contentType', 'category', 'contentGoal', 'tone', 'audience', 'style', 'templateAttributes.attribute'])
            ->findOrFail($validated['template_id']);

        $prompt = $promptBuilder->buildFromTemplate($template);

        return response()->json(['prompt' => $prompt]);
    }

    public function generate(Request $request, PromptBuilder $promptBuilder, AiService $aiService)
    {
        $validated = $request->validate([
            'template_id' => ['required', 'integer', 'exists:templates,id'],
        ]);

        $template = Template::with(['platform', 'contentType', 'category', 'contentGoal', 'tone', 'audience', 'style', 'templateAttributes.attribute'])
            ->findOrFail($validated['template_id']);

        $prompt = $promptBuilder->buildFromTemplate($template);

        $aiResponse = $aiService->generate($prompt);

        $content = GeneratedContent::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'platform_id' => $template->platform_id,
            'content_type_id' => $template->content_type_id,
            'content_type' => $template->contentType?->name ?? null,
            'category' => $template->category?->name ?? null,
            'prompt' => $prompt,
            'ai_response' => $aiResponse,
            'result' => $aiResponse,
            'status' => 'generated',
            'settings_json' => [
                'goal' => $template->contentGoal?->name ?? null,
                'tone' => $template->tone?->name ?? null,
                'audience' => $template->audience?->name ?? null,
                'style' => $template->style?->name ?? null,
                'length' => $template->length,
                'bulk_generate' => $template->bulk_generate,
            ],
        ]);

        return response()->json([
            'id' => $content->id,
            'template_id' => $content->template_id,
            'prompt' => $content->prompt,
            'ai_response' => $content->ai_response,
            'status' => $content->status,
            'created_at' => $content->created_at,
        ]);
    }
}
