<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPostReminderJob;
use App\Models\Post;
use App\Models\Reminder;
use Carbon\Carbon;

class CheckPostReminderCommand extends Command
{
    protected $signature = 'reminder:check';
    protected $description = 'Check if a post reminder needs to be sent';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();

        // Check if any post is scheduled for today
        $hasPost = Post::whereBetween('scheduled_at', [$startOfDay, $endOfDay])
            ->whereIn('status', ['scheduled', 'posted'])
            ->exists();

        if ($hasPost) {
            $this->info('Post scheduled for today, no reminder needed.');
            return;
        }

        // Check time window: 5:30 PM to 7:00 PM
        if (!$now->between(Carbon::createFromTime(17, 30), Carbon::createFromTime(19, 0))) {
            $this->info('Not in reminder time window.');
            return;
        }

        // Check if reminder already sent today
        $reminderSent = Reminder::where('date', $today)->whereNotNull('sent_at')->exists();
        if ($reminderSent) {
            $this->info('Reminder already sent today.');
            return;
        }

        // Dispatch job
        SendPostReminderJob::dispatch();
        $this->info('Reminder job dispatched.');
    }
}
