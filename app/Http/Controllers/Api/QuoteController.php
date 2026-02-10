<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $category = Category::firstOrCreate([
            'name' => $request->category,
            'slug' => Str::slug($request->category),
        ]);

        $quote = Quote::create([
            'quote' => $request->quote,
            'category' => $category->id,
            'status' => 'unused',
            'generated_at' => now(),
        ]);

        Post::create([
            'quote_id' => $quote->id,
            'caption' => $request->caption,
            'hook' => $request->hook,
            'image_path' => $request->image_url, // IMPORTANT: full URL
            'scheduled_at' => now()->setTime(9, 0),
            'status' => 'scheduled'
        ]);

        return response()->json(['success' => true]);
    }

    public function getQuotes(Request $request)
    {
        $quotes = Quote::latest()->limit(40)->pluck('quote');

        return response()->json($quotes);
    }
}
