<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Quote;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with(['post', 'categoryRelation']);

        // Filters
        if ($request->filled('search')) {
            $query->where('quote', 'like', "%{$request->search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'draft') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('post')
                        ->orWhereHas('post', function ($subQuery) {
                            $subQuery->where('status', 'draft');
                        });
                });
            } else {
                $query->whereHas('post', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        if ($sort === 'content') {
            $query->orderBy('quote', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $quotes = $query->paginate($request->per_page ?? 12)->withQueryString();

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

        return Inertia::render('Admin/Quotes/Index', [
            'quotes' => $quotes,
            'filters' => $request->all(['search', 'status', 'category', 'sort', 'direction']),
            'categories' => $categories,
            'statusCounts' => $statusCounts,
            'allCategoriesCount' => $allCategoriesCount,
        ]);
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'quote' => 'required|string',
            'category' => 'nullable|string',
            'caption' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $quote->update([
            'quote' => $request->quote,
            'category' => $request->category,
        ]);

        $postData = [
            'caption' => $request->caption,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('quotes', 'public');
            $postData['image_path'] = $path;
        }

        $quote->post()->updateOrCreate(
            ['quote_id' => $quote->id],
            $postData
        );

        return back()->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return back()->with('success', 'Quote deleted successfully.');
    }
}
