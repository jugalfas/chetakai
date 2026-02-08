<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Post;
use App\Models\Quote;
use App\Services\QuoteImageService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\FailedJob;

class GenerateDailyQuotesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $scheduled_at = Carbon::today()->setTime(9, 0, 0);
        Log::info('Starting quote generation');
        $recentQuotes = Quote::latest()
            ->take(40)
            ->pluck('quote')
            ->values();

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::post(env('N8N_GENERATE_QUOTE_URL'), [
            'number_of_quotes' => 1,
            'recent_quotes' => $recentQuotes,
        ]);

        if ($response->successful()) {
            $quotes = $response->json('quotes');
            if (!isset($quotes[0]['text'])) {
                Log::warning('Quote text is empty. Retrying...');
                $this->release(10);
                return;
            }

            $savedCount = 0;
            $exists = Quote::where('quote', $quotes[0]['text'])->exists();

            if ($exists) {
                Log::warning('Duplicate quote detected. Retrying...');
                $this->release(10);
                return;
            }

            if (!empty($quotes[0]['text'])) {
                // Store the quote
                $savedCount++;

                $category = Category::firstOrCreate([
                    'name' => $quotes[0]['category'],
                ], ['slug' => strtolower($quotes[0]['category'])]);

                $quote_db = Quote::create([
                    'quote' => $quotes[0]['text'],
                    'category' => $category->id,
                    'status' => 'unused',
                    'generated_at' => now(),
                ]);

                $imageUrl = QuoteImageService::generate($quotes[0]['text']);

                Post::create([
                    'quote_id' => $quote_db->id,
                    'caption' => $quotes[0]['caption'] ?? null,
                    'image_path' => $imageUrl,
                    'scheduled_at' => $scheduled_at,
                    'status' => 'scheduled',
                ]);
            }

            Log::info("{$savedCount} quotes saved.");
        } else {
            Log::error('Webhook failed: ' . $response->body());
            $this->release(30); // Retry after 30 seconds
        }
    }
}
