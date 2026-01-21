<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Quote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::query();

        // Filters
        if ($request->filled('search')) {
            $query->where('quote', 'like', "%{$request->search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        $quotes = $query->paginate(12)->withQueryString();

        // Status counts
        $statusCounts = [
            'all' => Quote::count(),
            'published' => Quote::where('status', 'published')->count(),
            'scheduled' => Quote::where('status', 'scheduled')->count(),
            'pending' => Quote::where('status', 'pending')->count(),
            'unused' => Quote::where('status', 'unused')->count(),
        ];

        // Categories with counts
        $categories = Category::where('is_active', true)->get()->map(function ($category) {
            $category->quotes_count = Quote::where('category', $category->name)->count();
            return $category;
        });

        return Inertia::render('Admin/Quotes/Index', [
            'quotes' => $quotes,
            'filters' => $request->all(['search', 'status', 'category', 'sort', 'direction']),
            'categories' => $categories,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return back()->with('success', 'Quote deleted successfully.');
    }
}
