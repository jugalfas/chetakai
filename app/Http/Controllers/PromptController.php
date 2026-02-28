<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $defaults = Prompt::whereNull('user_id')->get()->keyBy('type');
        $userPrompts = Prompt::where('user_id', $userId)->get()->keyBy('type');

        $types = ['post', 'reel'];
        $prompts = collect($types)->map(function ($type) use ($userPrompts, $defaults) {
            return $userPrompts->get($type) ?? $defaults->get($type);
        })->filter()->values();

        return Inertia::render('Prompt/Index', [
            'prompts' => $prompts,
        ]);
    }

    public function update(Request $request, Prompt $prompt)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'type' => 'required|in:post,reel',
        ]);

        $userId = Auth::id();
        if ($prompt->user_id === null || $prompt->user_id !== $userId) {
            $userPrompt = Prompt::updateOrCreate(
                ['user_id' => $userId, 'type' => $validated['type']],
                ['prompt' => $validated['prompt'], 'type' => $validated['type']]
            );
        } else {
            $prompt->update($validated);
        }

        return back()->with('success', 'Prompt updated.');
    }

    public function destroy(Prompt $prompt)
    {
        $prompt->delete();

        return back()->with('success', 'Prompt deleted.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'prompts' => 'required|array',
            'prompts.*.id' => 'nullable|integer|exists:prompts,id',
            'prompts.*.prompt' => 'required|string',
            'prompts.*.type' => 'required|in:post,reel',
        ]);

        $userId = Auth::id();
        foreach ($data['prompts'] as $p) {
            $existing = isset($p['id']) ? Prompt::find($p['id']) : null;
            if ($existing && $existing->user_id === $userId) {
                $existing->update(['prompt' => $p['prompt'], 'type' => $p['type']]);
                continue;
            }

            Prompt::updateOrCreate(
                ['user_id' => $userId, 'type' => $p['type']],
                ['prompt' => $p['prompt'], 'type' => $p['type']]
            );
        }

        return back()->with('success', 'Prompts saved.');
    }
}
