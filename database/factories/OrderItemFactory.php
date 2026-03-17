<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory();
        $price = $product->sale_price ?? $product->regular_price;
        $qty = fake()->numberBetween(1, 5);

        return [
            'order_id' => Order::inRandomOrder()->first()->id ?? Order::factory(),
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => $qty,
            'price' => $price,
            'subtotal' => round($price * $qty, 2),
            'options' => json_encode(['size' => fake()->word(), 'flavor' => fake()->word()]),
            'created_at' => fn($attr) => Order::find($attr['order_id'])?->created_at ?? now(),
            'updated_at' => fn($attr) => $attr['created_at'],
        ];
    }
}
