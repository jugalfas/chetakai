<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'Instagram', 'slug' => 'instagram'],
            ['name' => 'Facebook', 'slug' => 'facebook'],
            ['name' => 'LinkedIn', 'slug' => 'linkedin'],
            ['name' => 'X', 'slug' => 'x'],
            ['name' => 'YouTube', 'slug' => 'youtube'],
            ['name' => 'TikTok', 'slug' => 'tiktok'],
        ];

        foreach ($platforms as $platform) {
            Platform::updateOrCreate(
                ['slug' => $platform['slug']],
                array_merge($platform, ['status' => true])
            );
        }
    }
}
