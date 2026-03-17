<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Auto-generates unlimited unique category names
        $name = fake()->unique()->words(rand(2, 4), true) . ' ' . fake()->randomElement(['Cakes', 'Delights', 'Specials', 'Creations']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(rand(2, 4), true),
            'image' => 'categories/' . fake()->uuid() . '.jpg',
            'is_active' => fake()->boolean(90),
            'order' => fake()->numberBetween(1, 100),
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fn($attr) => fake()->dateTimeBetween($attr['created_at'], 'now'),
        ];
    }
}
