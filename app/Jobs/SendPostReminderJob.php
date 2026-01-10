<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Reminder;
use Carbon\Carbon;

class SendPostReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('Sending post reminder');

        $currentTime = Carbon::now()->format('h:i A');
        $payload = [
            'current_time' => $currentTime,
            'preferred_post_time' => '8:00 PM',
            'deadline_time' => '6:00 PM',
        ];

        $response = Http::timeout(30)->post(env('N8N_REMINDER_WEBHOOK_URL'), $payload);

        if ($response->successful()) {
            // Store reminder
            Reminder::updateOrCreate(
                ['date' => Carbon::today()->toDateString()],
                ['sent_at' => Carbon::now()]
            );
            Log::info('Reminder sent successfully');
        } else {
            Log::error('Failed to send reminder: ' . $response->body());
            throw new \Exception('Webhook failed');
        }
    }
}
