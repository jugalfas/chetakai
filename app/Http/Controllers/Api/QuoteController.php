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
        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // generate unique name
            $filename = 'quote_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            // store in storage/app/public/quotes
            $imagePath = $file->storeAs('quotes', $filename, 'public');
        }

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
            'image_path' => asset('storage/' . $imagePath), // IMPORTANT: full URL
            'scheduled_at' => now()->setTime(9, 0),
            'status' => 'scheduled'
        ]);

        return response()->json(['success' => true]);
    }

    public function getQuotes(Request $request)
    {
        $quotes = Quote::latest()->limit(40)->pluck('quote');

        return response()->json(["quotes" => $quotes]);
    }
}
