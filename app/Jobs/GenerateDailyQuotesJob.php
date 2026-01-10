<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\Quote;
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
        Log::info('Starting quote generation');

        $response = Http::post(env('N8N_GENERATE_QUOTE_URL'), [
            'number_of_quotes' => 5,
            'categories' => ['discipline', 'money', 'mindset'],
        ]);

        if ($response->successful()) {
            $quotes = $response->json('quotes');
            $savedCount = 0;

            foreach ($quotes as $quote) {
                if (!empty($quote['text']) && in_array($quote['category'], ['discipline', 'money', 'mindset'])) {
                    // Store the quote
                    $savedCount++;
                    $quote_db = Quote::create([
                        'quote' => $quote['text'],
                        'category' => $quote['category'],
                        'status' => 'unused',
                        'generated_at' => now(),
                    ]);

                    Post::create([
                        'quote_id' => $quote_db->id,
                        'caption' => $quote['caption'] ?? null,
                    ]);
                }
            }

            Log::info("{$savedCount} quotes saved.");
        } else {
            Log::error('Webhook failed: ' . $response->body());
            $this->release(30); // Retry after 30 seconds
        }
    }
}
