<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Str;

class QuoteImageService
{
    public static function generate($quote)
    {
        $html = view('quote-render', [
            'quote' => $quote
        ])->render();

        $filename = 'posts/' . Str::uuid() . '.jpg';
        $path = storage_path('app/public/' . $filename);

        Browsershot::html($html)
            ->noSandbox()
            ->setNodeBinary('/home/u230551100/.nvm/versions/node/v24.12.0/bin/node')
            ->setNpmBinary('/home/u230551100/.nvm/versions/node/v24.12.0/bin/npm')
            ->addChromiumArguments([
                '--disable-gpu',
                '--disable-dev-shm-usage',
                '--disable-setuid-sandbox',
                '--no-zygote',
                '--single-process',
                '--disable-extensions',
                '--disable-background-networking',
                '--disable-background-timer-throttling',
                '--disable-renderer-backgrounding',
                '--disable-sync',
                '--metrics-recording-only',
            ])
            ->windowSize(1080, 1350)
            ->waitUntilNetworkIdle(false)
            ->timeout(180)
            ->quality(100)
            ->save($path);

        return asset('storage/' . $filename);
    }
}
