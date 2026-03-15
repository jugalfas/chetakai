<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\StudioCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $cats = StudioCategory::where('user_id', Auth::id())->orderBy('name')->get();
        return response()->json($cats);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($validated['name']);
        $cat = StudioCategory::updateOrCreate(
            ['user_id' => Auth::id(), 'slug' => $slug],
            ['name' => $validated['name']]
        );

        return response()->json($cat);
    }

    public function destroy(StudioCategory $category)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $category->delete();
        return response()->json(['ok' => true]);
    }
}
