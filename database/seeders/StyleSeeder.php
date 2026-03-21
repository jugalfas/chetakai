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
            'Simple',
            'Storytelling',
            'Direct',
            'Educational',
            'Persuasive',
            'Minimal',
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
