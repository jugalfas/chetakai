<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (!$user) return;

        $user->notifications()->delete();

        $user->notify(new \App\Notifications\TestNotification(
            'Post Published', 
            'Your quote "The only way to do great work..." has been successfully posted to Instagram.'
        ));

        $user->notify(new \App\Notifications\TestNotification(
            'Subscription Update', 
            'Your Pro plan will renew in 3 days. Please ensure your billing info is up to date.'
        ));

        $user->notify(new \App\Notifications\TestNotification(
            'New Category Suggestion', 
            'Based on your recent quotes, we suggest creating a "Leadership" category.'
        ));
    }
}
