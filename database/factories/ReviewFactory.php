<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        $user = User::where('is_admin', false)->inRandomOrder()->first() ?? User::factory();
        $rating = fake()->randomElement([5, 5, 5, 4, 4, 4, 4, 3, 3, 2]);

        return [
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'rating' => $rating,
            'comment' => fake()->paragraphs(rand(1, 3), true),
            'is_approved' => fake()->boolean(80),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fn($attr) => fake()->dateTimeBetween($attr['created_at'], 'now'),
        ];
    }
}
