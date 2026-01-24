<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Jobs\PublishInstagramPostJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InstagramScheduledPostsCommand extends Command
{
    protected $signature = 'instagram:scheduled-posts';
    protected $description = 'Publish scheduled Instagram posts';

    public function handle()
    {
        try {
            // $posts = Post::where('status', 'scheduled')
            //    ->where('scheduled_at', '<=', Carbon::now())
            //    ->get();

            // foreach ($posts as $post) {
            //    PublishInstagramPostJob::dispatch($post);
            // }

            Log::info('Dispatched 0 jobs.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Command error: ' . $e->getMessage());
        }
    }
}
