<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Quote;
use App\Http\Requests\SchedulePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $quotes = Quote::with('post')
            ->when($request->search, function ($q, $search) {
                $q->where('quote', 'like', "%{$search}%");
            })
            ->when($request->sort, function ($q, $sort) use ($request) {
                $q->orderBy($sort, $request->direction ?? 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Quote/Index', [
            'quotes' => $quotes,
            'filters' => $request->only(['search']),
            'categories' => Category::where('is_active', true)->get(),
            'canvaClientId' => config('services.canva.client_id'),
        ]);
    }

    public function generate(Request $request)
    {
        $response = Http::timeout(60)->post(env('N8N_GENERATE_QUOTE_URL'), $request->all());

        if (!$response->successful()) {
            return response()->json([
                'error' => 'AI generation failed'
            ], 500);
        }

        $data = $response->json();

        // Guard against bad AI response
        if (
            empty($data['quote']) ||
            empty($data['category']) ||
            empty($data['mood'])
        ) {
            return response()->json([
                'error' => 'Invalid AI response'
            ], 500);
        }

        Quote::create([
            'quote' => $data['quote'],
            'category' => $data['category'],
            'mood' => $data['mood'],
            'status' => 'unused',
        ]);

        return response()->json(['success' => true]);
    }

    public function schedule(SchedulePostRequest $request, Quote $quote)
    {
        $post = $quote->post;

        if (!$post || $post->status !== 'draft') {
            return response()->json(['error' => 'Post is not available for scheduling'], 400);
        }

        if (empty($post->image_path)) {
            return response()->json(['error' => 'Post image is not ready yet'], 400);
        }

        DB::transaction(function () use ($request, $post, $quote) {
            $post->update([
                'scheduled_at' => $request->scheduled_at,
                'status' => 'scheduled',
            ]);
        });

        return redirect()->back()->with('success', 'Post scheduled successfully.');
    }

    // UPDATE quote
    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'quote' => 'required|string|max:1000',
            'category' => 'required|string|max:255',
            'caption' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:5120', // 5MB
        ]);

        DB::transaction(function () use ($validated, $quote, $request) {
            $quote->update([
                'quote' => $validated['quote'],
                'category' => $validated['category'],
            ]);

            if ($quote->post) {
                $updateData = [];
                if (isset($validated['caption'])) {
                    $updateData['caption'] = $validated['caption'];
                }
                if ($request->hasFile('image')) {
                    $path = $request->file('image')->store('posts', 'public');
                    $updateData['image_path'] = $path;
                }
                if (!empty($updateData)) {
                    $quote->post->update($updateData);
                }
            }
        });

        return redirect()->back()->with('success', 'Quote updated successfully.');
    }

    // DELETE quote
    public function destroy(Quote $quote)
    {
        $quote->delete();

        return redirect()->back()->with('success', 'Quote deleted successfully.');
    }
}
