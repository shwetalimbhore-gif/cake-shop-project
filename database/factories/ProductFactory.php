<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        // Auto-generates unlimited unique product names
        $name = fake()->unique()->words(rand(3, 6), true) . ' Cake';

        $regularPrice = fake()->randomFloat(2, 20, 200);
        $hasSale = fake()->boolean(30);
        $salePrice = $hasSale ? round($regularPrice * fake()->randomFloat(2, 0.6, 0.9), 2) : null;
        $stock = fake()->numberBetween(0, 100);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'short_description' => fake()->paragraph(rand(2, 3)),
            'description' => fake()->paragraphs(rand(4, 8), true),
            'regular_price' => $regularPrice,
            'sale_price' => $salePrice,
            'sku' => strtoupper(fake()->unique()->bothify('PROD-###-???')),
            'stock_quantity' => $stock,
            'in_stock' => $stock > 0,
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'featured_image' => 'products/' . fake()->uuid() . '.jpg',
            'gallery_images' => json_encode(array_fill(0, rand(3, 5), 'products/' . fake()->uuid() . '.jpg')),
            'sizes' => json_encode(fake()->randomElements(
                ['6"', '8"', '10"', '12"', 'Quarter Sheet', 'Half Sheet', 'Full Sheet'], rand(2, 4)
            )),
            'size_prices' => json_encode(array_map(fn() => $regularPrice * fake()->randomFloat(2, 0.8, 1.5), range(1, rand(2, 4)))),
            'flavors' => json_encode(fake()->randomElements(
                ['Vanilla', 'Chocolate', 'Strawberry', 'Red Velvet', 'Lemon', 'Carrot', 'Coconut'], rand(1, 3)
            )),
            'flavor_prices' => json_encode(array_map(fn($i) => $i === 0 ? 0 : fake()->randomFloat(2, 2, 8), range(0, rand(1, 3)-1))),
            'is_featured' => fake()->boolean(20),
            'is_eggless' => fake()->boolean(30),
            'is_active' => fake()->boolean(95),
            'views' => fake()->numberBetween(0, 10000),
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fn($attr) => fake()->dateTimeBetween($attr['created_at'], 'now'),
        ];
    }
}
