<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use App\Models\Category;
use App\Models\ContentGoal;
use App\Models\ContentType;
use App\Models\Style;
use App\Models\Tone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PromptManagementController extends Controller
{
    public function contentTypes()
    {
        return Inertia::render('Admin/PromptManagement/ContentTypes', [
            'items' => ContentType::orderBy('name')->get(),
        ]);
    }

    public function contentGoals()
    {
        return Inertia::render('Admin/PromptManagement/ContentGoals', [
            'items' => ContentGoal::orderBy('name')->get(),
        ]);
    }

    public function categories()
    {
        return Inertia::render('Admin/PromptManagement/Categories', [
            'items' => Category::orderBy('name')->get(),
        ]);
    }

    public function audiences()
    {
        return Inertia::render('Admin/PromptManagement/Audiences', [
            'items' => Audience::orderBy('name')->get(),
        ]);
    }

    public function styles()
    {
        return Inertia::render('Admin/PromptManagement/Styles', [
            'items' => Style::orderBy('name')->get(),
        ]);
    }

    public function tones()
    {
        return Inertia::render('Admin/PromptManagement/Tones', [
            'items' => Tone::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource' => 'required|string|in:content_types,content_goals,categories,audiences,styles,tones',
            'id' => 'nullable|integer',
            'title' => 'required|string|max:255',
        ]);

        $model = $this->getModelForResource($validated['resource']);

        $attributes = ['title' => $validated['title']];

        if ($validated['resource'] === 'categories') {
            $attributes = ['name' => $validated['title']];
        }

        /** @var \Illuminate\Database\Eloquent\Model $record */
        $record = $model::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $attributes
        );

        return back()->with('success', 'Saved successfully.');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'resource' => 'required|string|in:content_types,content_goals,categories,audiences,styles,tones',
            'id' => 'required|integer',
        ]);

        $model = $this->getModelForResource($validated['resource']);

        $record = $model::findOrFail($validated['id']);
        $record->delete();

        return back()->with('success', 'Deleted successfully.');
    }

    protected function getModelForResource(string $resource)
    {
        return match ($resource) {
            'content_types' => ContentType::class,
            'content_goals' => ContentGoal::class,
            'categories' => Category::class,
            'audiences' => Audience::class,
            'styles' => Style::class,
            'tones' => Tone::class,
            default => abort(404),
        };
    }
}

