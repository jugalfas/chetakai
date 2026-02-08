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
            ->windowSize(1080, 1350)
            ->waitUntilNetworkIdle()
            ->quality(100)
            ->save($path);

        return asset('storage/' . $filename);
    }
}
