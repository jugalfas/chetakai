<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentGoal;
use App\Models\ContentType;
use App\Models\ContentTypeAttribute;
use App\Models\Template;
use App\Models\TemplateAttribute;
use App\Models\Audience;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Style;
use App\Models\Tone;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    public function index(): Response
    {
        $templates = Template::query()
            ->with(['platform', 'contentType', 'category', 'contentGoal', 'tone', 'audience', 'style'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Templates/Index', [
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Templates/Create', [
            'platforms' => Platform::query()->where('status', true)->orderBy('name')->get(),
            'contentTypes' => ContentType::query()->where('status', true)->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'contentGoals' => ContentGoal::query()->orderBy('name')->get(),
            'tones' => Tone::query()->orderBy('name')->get(),
            'audiences' => Audience::query()->orderBy('name')->get(),
            'styles' => Style::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_name' => ['required', 'string', 'max:255'],
            'platform_id' => ['nullable', 'exists:platforms,id'],
            'content_type_id' => ['nullable', 'exists:content_types,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'goal_id' => ['nullable', 'exists:content_goals,id'],
            'tone_id' => ['nullable', 'exists:tones,id'],
            'audience_id' => ['nullable', 'exists:audiences,id'],
            'style_id' => ['nullable', 'exists:styles,id'],
            'length' => ['nullable', 'in:short,medium,long'],
            'bulk_generate' => ['nullable', 'integer', 'min:0'],
            'template_attributes' => ['array'],
            'template_attributes.*.attribute_id' => ['required', 'integer', 'exists:attributes,id'],
            'template_attributes.*.value' => ['nullable', 'string'],
        ]);

        $template = Template::create([
            'user_id' => null,
            'platform_id' => $validated['platform_id'] ?? null,
            'content_type_id' => $validated['content_type_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'goal_id' => $validated['goal_id'] ?? null,
            'tone_id' => $validated['tone_id'] ?? null,
            'audience_id' => $validated['audience_id'] ?? null,
            'style_id' => $validated['style_id'] ?? null,
            'template_name' => $validated['template_name'],
            'length' => $validated['length'] ?? null,
            'bulk_generate' => $validated['bulk_generate'] ?? 0,
        ]);

        if (!empty($validated['template_attributes'])) {
            foreach ($validated['template_attributes'] as $item) {
                TemplateAttribute::create([
                    'template_id' => $template->id,
                    'attribute_id' => $item['attribute_id'],
                    'value' => $item['value'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(Template $template): Response
    {
        $template->load(['platform', 'contentType', 'category', 'contentGoal', 'tone', 'audience', 'style', 'templateAttributes.attribute']);

        return Inertia::render('Admin/Templates/Edit', [
            'template' => $template,
            'platforms' => Platform::query()->where('status', true)->orderBy('name')->get(),
            'contentTypes' => ContentType::query()->where('status', true)->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'contentGoals' => ContentGoal::query()->orderBy('name')->get(),
            'tones' => Tone::query()->orderBy('name')->get(),
            'audiences' => Audience::query()->orderBy('name')->get(),
            'styles' => Style::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'template_name' => ['required', 'string', 'max:255'],
            'platform_id' => ['nullable', 'exists:platforms,id'],
            'content_type_id' => ['nullable', 'exists:content_types,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'goal_id' => ['nullable', 'exists:content_goals,id'],
            'tone_id' => ['nullable', 'exists:tones,id'],
            'audience_id' => ['nullable', 'exists:audiences,id'],
            'style_id' => ['nullable', 'exists:styles,id'],
            'length' => ['nullable', 'in:short,medium,long'],
            'bulk_generate' => ['nullable', 'integer', 'min:0'],
            'template_attributes' => ['array'],
            'template_attributes.*.attribute_id' => ['required', 'integer', 'exists:attributes,id'],
            'template_attributes.*.value' => ['nullable', 'string'],
        ]);

        $template->update([
            'platform_id' => $validated['platform_id'] ?? null,
            'content_type_id' => $validated['content_type_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'goal_id' => $validated['goal_id'] ?? null,
            'tone_id' => $validated['tone_id'] ?? null,
            'audience_id' => $validated['audience_id'] ?? null,
            'style_id' => $validated['style_id'] ?? null,
            'template_name' => $validated['template_name'],
            'length' => $validated['length'] ?? null,
            'bulk_generate' => $validated['bulk_generate'] ?? 0,
        ]);

        $template->templateAttributes()->delete();

        if (!empty($validated['template_attributes'])) {
            foreach ($validated['template_attributes'] as $item) {
                TemplateAttribute::create([
                    'template_id' => $template->id,
                    'attribute_id' => $item['attribute_id'],
                    'value' => $item['value'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        $template->delete();

        return back()->with('success', 'Template deleted successfully.');
    }

    public function contentTypeAttributes(ContentType $contentType)
    {
        $attributes = ContentTypeAttribute::query()
            ->where('content_type_id', $contentType->id)
            ->with(['attribute.attributeValues'])
            ->orderBy('display_order')
            ->get();

        $payload = $attributes->map(function ($item) {
            return [
                'id' => $item->attribute->id,
                'name' => $item->attribute->name,
                'slug' => $item->attribute->slug ?? null,
                'input_type' => $item->attribute->input_type ?? 'text',
                'is_required' => (bool) $item->is_required,
                'attribute_values' => $item->attribute->attributeValues->map(fn($v) => [
                    'id' => $v->id,
                    'value' => $v->value ?? $v->name ?? null,
                ]),
            ];
        });

        return response()->json(['attributes' => $payload]);
    }
}
