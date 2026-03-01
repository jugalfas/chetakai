<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchedulePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote' => 'required|string|max:1000',
            'caption' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'quote' => $validated['quote'],
            'caption' => $validated['caption'] ?? null,
            'status' => Post::STATUS_DRAFT,
            'type' => Post::TYPE_POST,
            'user_id' => Auth::user()->id ?? 1,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $data['image_path'] = $path;
        }

        Post::create($data);

        return redirect()->back()->with('success', 'Post created successfully.');
    }

    public function index(Request $request)
    {
        $posts = Post::query()
            ->when($request->search, function ($q, $search) {
                $q->where('quote', 'like', "%{$search}%");
            })
            ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
                $status = $request->status;
                $q->where('status', $status);
            })
            ->when($request->sort, function ($q, $sort) use ($request) {
                $orderBy = $sort === 'content' ? 'quote' : $sort;
                $q->orderBy($orderBy, $request->direction ?? 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        $baseCountQuery = Post::query()
            ->when($request->search, function ($q, $search) {
                $q->where('quote', 'like', "%{$search}%");
            });

        $statusCounts = [
            'all' => (clone $baseCountQuery)->count(),
            'published' => (clone $baseCountQuery)->where('status', Post::STATUS_POSTED)->count(),
            'scheduled' => (clone $baseCountQuery)->where('status', Post::STATUS_SCHEDULED)->count(),
            'draft' => (clone $baseCountQuery)->where('status', Post::STATUS_DRAFT)->count(),
        ];

        $allCategoriesCount = (clone $baseCountQuery)
            ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
                $q->where('status', $request->status);
            })->count();

        return Inertia::render('Post/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'status', 'sort', 'direction', 'per_page']),
            'categories' => [],
            'statusCounts' => $statusCounts,
            'allCategoriesCount' => $allCategoriesCount,
            'canvaClientId' => config('services.canva.client_id'),
        ]);
    }

    public function schedule(SchedulePostRequest $request, Post $post)
    {
        if ($post->status === Post::STATUS_POSTED) {
            return back()->withErrors(['error' => 'Post is not available for scheduling']);
        }

        if (empty($post->image_path)) {
            return back()->withErrors(['error' => 'Post image is not ready yet']);
        }

        DB::transaction(function () use ($request, $post) {
            $post->update([
                'scheduled_at' => $request->scheduled_at,
                'status' => Post::STATUS_SCHEDULED,
            ]);
        });

        return redirect()->back()->with('success', 'Post scheduled successfully.');
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'quote' => 'required|string|max:1000',
            'caption' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:5120',
        ]);

        DB::transaction(function () use ($validated, $post, $request) {
            $data = [
                'quote' => $validated['quote'],
            ];

            if (isset($validated['caption'])) {
                $data['caption'] = $validated['caption'];
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('posts', 'public');
                $data['image_path'] = $path;
            }

            $post->update($data);
        });

        return redirect()->back()->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }

    public function render($id)
    {
        $post = Post::findOrFail($id);

        return view('quote-render', [
            'quote' => $post->quote
        ]);
    }
}
