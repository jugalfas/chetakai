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
            ['name' => 'LinkedIn', 'slug' => 'linkedin'],
            ['name' => 'Twitter', 'slug' => 'twitter'],
        ];

        foreach ($platforms as $platform) {
            Platform::updateOrCreate(
                ['slug' => $platform['slug']],
                array_merge($platform, ['status' => true])
            );
        }
    }
}
