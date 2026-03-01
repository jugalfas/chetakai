<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Prompt;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'quote_' . time() . '_' . Str::random(6) . '.png';
            $imagePath = $file->storeAs('posts', $filename, 'public');
        }

        Post::create([
            'quote' => $request->quote,
            'user_id' => 1,
            'caption' => $request->caption,
            'hook' => $request->hook,
            'image_path' => $imagePath ? asset('storage/' . $imagePath) : null, // IMPORTANT: full URL
            'scheduled_at' => now()->setTime(9, 0),
            'status' => 'scheduled'
        ]);

        return response()->json(['success' => true]);
    }

    public function getPrompt(Request $request)
    {
        $posts = Post::latest()->limit(40)->pluck('quote');
        $prompt = Prompt::where('type', 'post')->where('user_id', 1)->first()->prompt;

        $prompt = str_replace('[quotes]', $posts->implode("\n"), $prompt);

        return response()->json(["prompt" => $prompt]);
    }

    public function update_media_id_in_post(Request $request)
    {
        $post = Post::find($request->post_id);

        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found']);
        }

        $post->media_id = $request->media_id;
        $post->save();

        return response()->json(['success' => true]);
    }

    public function get_post_for_upload(Request $request)
    {
        $user = User::where('email', 'jugal@chetak.ai')->first();
        $post = Post::where('status', 'scheduled')->whereDate('scheduled_at', now())->first();

        if (!$post) {
            return response()->json(['success' => false, 'message' => 'No scheduled posts found']);
        }

        $payload = [
            'post_id' => $post->id,
            'image_url' => $post->image_path,
            'caption' => $post->caption,
            'scheduled_at' => $post->scheduled_at->toIso8601String(),
            'instagram_access_token' => decrypt($user->instagram_access_token),
            'instagram_business_id' => $user->instagram_business_id,
        ];
        return response()->json($payload);
    }
}
