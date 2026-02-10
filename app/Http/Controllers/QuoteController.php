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
use Illuminate\Support\Str;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $quotes = Quote::with('post', 'categoryRelation')
            ->when($request->search, function ($q, $search) {
                $q->where('quote', 'like', "%{$search}%");
            })
            ->when($request->category, function ($q, $categoryId) {
                $q->where('category', $categoryId);
            })
            ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
                $status = $request->status;
                if ($status === 'draft') {
                    $q->where(function ($query) {
                        $query->whereDoesntHave('post')
                            ->orWhereHas('post', function ($subQuery) {
                                $subQuery->where('status', 'draft');
                            });
                    });
                } else {
                    $q->whereHas('post', function ($query) use ($status) {
                        $query->where('status', $status);
                    });
                }
            })
            ->when($request->sort, function ($q, $sort) use ($request) {
                $q->orderBy($sort, $request->direction ?? 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        // Base query for counts, filtered by search
        $baseCountQuery = Quote::query()
            ->when($request->search, function ($q, $search) {
                $q->where('quote', 'like', "%{$search}%");
            });

        // Status counts for the tabs (filtered by search AND category)
        $statusCountsQuery = (clone $baseCountQuery)
            ->when($request->category, function ($q, $categoryId) {
                $q->where('category', $categoryId);
            });

        $statusCounts = [
            'all' => (clone $statusCountsQuery)->count(),
            'published' => (clone $statusCountsQuery)->whereHas('post', function ($q) {
                $q->where('status', 'posted');
            })->count(),
            'scheduled' => (clone $statusCountsQuery)->whereHas('post', function ($q) {
                $q->where('status', 'scheduled');
            })->count(),
            'draft' => (clone $statusCountsQuery)->where(function ($q) {
                $q->whereDoesntHave('post')
                    ->orWhereHas('post', function ($subQuery) {
                        $subQuery->where('status', 'draft');
                    });
            })->count(),
        ];

        // Total count for "All Categories" dropdown (filtered by search AND status, but NOT category)
        $allCategoriesCount = (clone $baseCountQuery)
            ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
                $status = $request->status;
                if ($status === 'draft') {
                    $q->where(function ($query) {
                        $query->whereDoesntHave('post')
                            ->orWhereHas('post', function ($subQuery) {
                                $subQuery->where('status', 'draft');
                            });
                    });
                } else {
                    $q->whereHas('post', function ($query) use ($status) {
                        $query->where('status', $status);
                    });
                }
            })->count();

        // Categories with counts (filtered by search AND status)
        $categories = Category::withCount(['quotes' => function ($query) use ($request) {
            $query->when($request->search, function ($q, $search) {
                $q->where('quote', 'like', "%{$search}%");
            })
                ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
                    $status = $request->status;
                    if ($status === 'draft') {
                        $q->where(function ($query) {
                            $query->whereDoesntHave('post')
                                ->orWhereHas('post', function ($subQuery) {
                                    $subQuery->where('status', 'draft');
                                });
                        });
                    } else {
                        $q->whereHas('post', function ($query) use ($status) {
                            $query->where('status', $status);
                        });
                    }
                });
        }])->where('is_active', true)->get();

        return Inertia::render('Quote/Index', [
            'quotes' => $quotes,
            'filters' => $request->only(['search', 'status', 'category', 'sort', 'direction']),
            'categories' => $categories,
            'statusCounts' => $statusCounts,
            'allCategoriesCount' => $allCategoriesCount,
            'canvaClientId' => config('services.canva.client_id'),
        ]);
    }

    public function schedule(SchedulePostRequest $request, Quote $quote)
    {
        $post = $quote->post;

        if (!$post || $post->status === 'posted') {
            return back()->withErrors(['error' => 'Post is not available for scheduling']);
        }

        if (empty($post->image_path)) {
            return back()->withErrors(['error' => 'Post image is not ready yet']);
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

            $postData = [];
            if (isset($validated['caption'])) {
                $postData['caption'] = $validated['caption'];
            }
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('posts', 'public');
                $postData['image_path'] = $path;
            }

            if (!empty($postData)) {
                $quote->post()->updateOrCreate(
                    ['quote_id' => $quote->id],
                    $postData
                );
            }
        });

        return redirect()->back()->with('success', 'Quote updated successfully.');
    }

    // DELETE quote
    public function destroy(Quote $quote)
    {
        // Check if the quote has a post
        if ($quote->post) {
            // Delete the associated post
            $quote->post->delete();
        }

        $quote->delete();

        return redirect()->back()->with('success', 'Quote deleted successfully.');
    }

    // Render quote
    public function render($id)
    {
        $quote = Quote::findOrFail($id);

        return view('quote-render', [
            'quote' => $quote->quote
        ]);
    }
}
