<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zip' => fake()->postcode(),
            'country' => fake()->country(),
            'avatar' => null,
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fn($attr) => fake()->dateTimeBetween($attr['created_at'], 'now'),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'is_admin' => true,
            'email' => 'admin@cakeshop.com',
            'name' => 'Admin User',
        ]);
    }
}
