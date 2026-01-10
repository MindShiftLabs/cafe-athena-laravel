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
            'user_firstname' => fake()->firstName(),
            'user_lastname' => fake()->lastName(),
            'user_email' => fake()->unique()->safeEmail(),
            'user_birthday' => fake()->date(),
            'user_password' => static::$password ??= Hash::make('password'),
            'user_role' => 'customer',
            'user_phone' => fake()->phoneNumber(),
            'user_address' => fake()->address(),
            'user_createdat' => now(),
            'user_updatedat' => now(),
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
