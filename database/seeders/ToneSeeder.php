<?php

namespace Database\Seeders;

use App\Models\Tone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ToneSeeder extends Seeder
{
    public function run(): void
    {
        $tones = [
            'Professional',
            'Casual',
            'Emotional',
            'Motivational',
            'Funny',
            'Inspirational',
            'Educational',
        ];

        foreach ($tones as $tone) {
            Tone::updateOrCreate(
                ['slug' => Str::slug($tone)],
                [
                    'name' => $tone,
                    'status' => true,
                ]
            );
        }
    }
}
