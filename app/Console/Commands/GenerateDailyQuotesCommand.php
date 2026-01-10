<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailyQuotesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateDailyQuotesCommand extends Command
{
    protected $signature = 'quotes:generate';
    protected $description = 'Generate daily quotes';

    public function handle()
    {
        // Dispatch the job
        GenerateDailyQuotesJob::dispatch();
    }
}
