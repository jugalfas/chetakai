<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\User;

class PublishInstagramPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $user = User::where('email', 'jugal@chetak.ai')->first();

        $imageUrl = asset('storage/' . $this->post->image_path);
        Log::info('Image URL: ' . $imageUrl);
        $payload = [
            'post_id' => $this->post->id,
            'image_url' => $imageUrl,
            'caption' => $this->post->caption,
            'scheduled_at' => $this->post->scheduled_at->toIso8601String(),
            'instagram_access_token' => decrypt($user->instagram_access_token),
            'instagram_business_id' => $user->instagram_business_id,
        ];

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(30)->post(env('N8N_INSTAGRAM_WEBHOOK_URL'), $payload);

        if ($response->successful()) {
            $status = $response->json('status');
            if ($status === 'posted') {
                $this->post->update(['status' => 'posted']);

                $this->post->quote->update(['status' => 'posted']);
                Log::info('Post ' . $this->post->id . ' published successfully.');
            } elseif ($status === 'failed') {
                $this->post->update(['status' => 'failed']);
                Log::error('Post ' . $this->post->id . ' failed to publish.');
            } else {
                Log::warning('Post ' . $this->post->id . ' unknown status: ' . $status);
            }
        } else {
            Log::error('Failed to send post ' . $this->post->id . ' to webhook: ' . $response->body());
            throw new \Exception('Webhook failed');
        }
    }
}
