<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Jugal',
            'last_name' => 'Faswala',
            'email' => 'jugal@chetak.ai',
            'email_verified_at' => now(),
            'password' => Hash::make('Jugal@7177'),
            'instagram_username' => 'chetak_quotes',
            'instagram_account_type' => 'Business Account',
            'instagram_connected' => false,
            'instagram_access_token' => NULL,
            'auto_post_schedule_enabled' => true,
            'post_notifications_enabled' => true,
        ]);
    }
}
