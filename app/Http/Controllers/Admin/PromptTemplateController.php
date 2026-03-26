<?php

namespace App\Http\Controllers\Admin;

use App\Ai\Agents\ChetakAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromptTemplateRequest;
use App\Http\Requests\Admin\UpdatePromptTemplateRequest;
use App\Models\ContentType;
use App\Models\Platform;
use App\Models\PromptTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Ai;

class PromptTemplateController extends Controller
{
    public function index(Request $request): Response
    {
        $promptTemplates = PromptTemplate::query()
            ->with(['platform', 'contentType'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('prompt_template', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/PromptTemplates/Index', [
            'promptTemplates' => $promptTemplates,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        $platforms = Platform::query()->where('status', true)->orderBy('name')->get();
        $contentTypes = ContentType::query()->where('status', true)->orderBy('name')->get();

        return Inertia::render('Admin/PromptTemplates/Create', [
            'platforms' => $platforms,
            'contentTypes' => $contentTypes,
        ]);
    }

    public function store(StorePromptTemplateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['is_default']) {
            $this->ensureOnlyOneDefault($validated['platform_id'], $validated['content_type_id']);
        }

        PromptTemplate::create($validated);

        return redirect()
            ->route('admin.prompt-templates.index')
            ->with('success', 'Prompt template created successfully.');
    }

    public function edit(PromptTemplate $promptTemplate): Response
    {
        $promptTemplate->load(['platform', 'contentType']);
        $platforms = Platform::query()->where('status', true)->orderBy('name')->get();
        $contentTypes = ContentType::query()->where('status', true)->orderBy('name')->get();

        return Inertia::render('Admin/PromptTemplates/Edit', [
            'promptTemplate' => $promptTemplate,
            'platforms' => $platforms,
            'contentTypes' => $contentTypes,
        ]);
    }

    public function update(UpdatePromptTemplateRequest $request, PromptTemplate $promptTemplate): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['is_default']) {
            $this->ensureOnlyOneDefault($validated['platform_id'], $validated['content_type_id'], $promptTemplate->id);
        }

        $promptTemplate->update($validated);

        return redirect()
            ->route('admin.prompt-templates.index')
            ->with('success', 'Prompt template updated successfully.');
    }

    public function destroy(PromptTemplate $promptTemplate): RedirectResponse
    {
        $promptTemplate->delete();

        return back()->with('success', 'Prompt template deleted successfully.');
    }

    public function toggleStatus(PromptTemplate $promptTemplate): RedirectResponse
    {
        $promptTemplate->update(['status' => !$promptTemplate->status]);

        return back()->with('success', $promptTemplate->status ? 'Prompt template activated.' : 'Prompt template deactivated.');
    }

    public function test(Request $request, PromptTemplate $promptTemplate)
    {
        $validated = $request->validate([
            'test_variables' => 'required|array',
        ]);

        $templateVariables = $promptTemplate->variables ?? [];
        $testVariables = $validated['test_variables'];

        $variables = array_merge($templateVariables, $testVariables);

        $template = $promptTemplate->prompt_template;

        $finalPrompt = preg_replace_callback('/\{\{([a-zA-Z0-9_-]+)\}\}/', function ($matches) use ($variables) {
            $key = $matches[1];
            return $variables[$key] ?? $matches[0];
        }, $template);

        // 🔥 REAL AI CALL
        $response = (new ChetakAgent)->prompt($finalPrompt);

        return response()->json([
            'final_prompt' => $finalPrompt,
            'parsed_variables' => $variables,
            'ai_response' => json_decode($response->text, true),
        ]);
    }

    private function ensureOnlyOneDefault($platformId, $contentTypeId, $excludeId = null)
    {
        PromptTemplate::query()
            ->where('platform_id', $platformId)
            ->where('content_type_id', $contentTypeId)
            ->where('is_default', true)
            ->when($excludeId, function ($query, $id) {
                $query->where('id', '!=', $id);
            })
            ->update(['is_default' => false]);
    }
}
