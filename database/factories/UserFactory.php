<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
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
            'remember_token' => NULL,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
