<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'content_type' => 'required|string',
            'settings_json' => 'required|array',
        ]);

        $template = Template::create([
            'user_id' => Auth::id(),
            'template_name' => $validated['template_name'],
            'content_type' => $validated['content_type'],
            'settings_json' => $validated['settings_json'],
        ]);

        return response()->json($template);
    }

    public function update(Request $request, Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'settings_json' => 'required|array',
        ]);

        $template->update($validated);
        return response()->json($template);
    }

    public function destroy(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $template->delete();
        return response()->json(['ok' => true]);
    }

    public function duplicate(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $copy = $template->replicate();
        $copy->template_name = $template->template_name.' Copy';
        $copy->save();
        return response()->json($copy);
    }
}

