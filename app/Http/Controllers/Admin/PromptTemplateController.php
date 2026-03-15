<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentType;
use App\Models\Platform;
use App\Models\PromptTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromptTemplateController extends Controller
{
    public function index(): Response
    {
        $promptTemplates = PromptTemplate::query()
            ->with(['platform', 'contentType'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/PromptTemplates/Index', [
            'promptTemplates' => $promptTemplates,
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform_id' => ['nullable', 'exists:platforms,id'],
            'content_type_id' => ['nullable', 'exists:content_types,id'],
            'prompt_template' => ['required', 'string'],
            'status' => ['boolean'],
        ]);

        $validated['status'] = $request->boolean('status', true);
        $validated['platform_id'] = $request->input('platform_id') ?: null;
        $validated['content_type_id'] = $request->input('content_type_id') ?: null;

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

    public function update(Request $request, PromptTemplate $promptTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform_id' => ['nullable', 'exists:platforms,id'],
            'content_type_id' => ['nullable', 'exists:content_types,id'],
            'prompt_template' => ['required', 'string'],
            'status' => ['boolean'],
        ]);

        $validated['status'] = $request->boolean('status', true);
        $validated['platform_id'] = $request->input('platform_id') ?: null;
        $validated['content_type_id'] = $request->input('content_type_id') ?: null;

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
}
