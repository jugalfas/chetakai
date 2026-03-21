<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $styles = [
            'Language',
            'Cta Type',
            'Hashtag Style',
            'Emoji Level',
            'Banned Words',
            'Include Keywords',
            'Avoid Keywords',
        ];

        foreach ($styles as $style) {
            Attribute::updateOrCreate(
                ['slug' => Str::slug($style)],
                [
                    'name' => $style,
                    'type' => 'text',
                    'description' => $style,
                    'status' => true,
                ]
            );
        }
    }
}
