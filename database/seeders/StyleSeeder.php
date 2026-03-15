<?php

namespace Database\Seeders;

use App\Models\Style;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StyleSeeder extends Seeder
{
    public function run(): void
    {
        $styles = [
            'Minimal',
            'Advice Style',
            'Journal Style',
            'Story Style',
            'Educational',
            'Motivational',
            'Instagram Viral',
        ];

        foreach ($styles as $style) {
            Style::updateOrCreate(
                ['slug' => Str::slug($style)],
                [
                    'name' => $style,
                    'status' => true,
                ]
            );
        }
    }
}
