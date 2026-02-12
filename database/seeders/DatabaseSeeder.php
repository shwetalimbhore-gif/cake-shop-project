<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@cakeshop.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create regular user
        User::create([
            'name' => 'Customer',
            'email' => 'customer@cakeshop.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Birthday Cakes', 'description' => 'Perfect for birthdays', 'is_active' => true],
            ['name' => 'Wedding Cakes', 'description' => 'Elegant wedding cakes', 'is_active' => true],
            ['name' => 'Cupcakes', 'description' => 'Delicious bite-sized treats', 'is_active' => true],
            ['name' => 'Custom Cakes', 'description' => 'Designed just for you', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample products
        $products = [
            [
                'name' => 'Chocolate Cake',
                'short_description' => 'Rich and moist chocolate cake',
                'description' => 'Our signature chocolate cake with creamy chocolate frosting. Perfect for any occasion!',
                'regular_price' => 29.99,
                'sale_price' => 24.99,
                'sku' => 'CAKE-BDAY-CHOC-001',
                'stock_quantity' => 15,
                'category_id' => 1, // Birthday Cakes
                'is_active' => true,
                'is_featured' => true,
                'sizes' => json_encode(['small', 'medium', 'large']),
                'flavors' => json_encode(['chocolate']),
            ],
            [
                'name' => 'Vanilla Birthday Cake',
                'short_description' => 'Classic vanilla cake',
                'description' => 'Light and fluffy vanilla cake with buttercream frosting. A timeless favorite!',
                'regular_price' => 27.99,
                'sale_price' => null,
                'sku' => 'CAKE-BDAY-VAN-002',
                'stock_quantity' => 10,
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => false,
                'sizes' => json_encode(['small', 'medium', 'large']),
                'flavors' => json_encode(['vanilla']),
            ],
            [
                'name' => 'Red Velvet Cake',
                'short_description' => 'Classic red velvet with cream cheese frosting',
                'description' => 'Stunning red velvet cake with rich cream cheese frosting. A showstopper!',
                'regular_price' => 32.99,
                'sale_price' => 28.99,
                'sku' => 'CAKE-BDAY-RED-003',
                'stock_quantity' => 8,
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => true,
                'sizes' => json_encode(['medium', 'large']),
                'flavors' => json_encode(['red_velvet']),
            ],
            [
                'name' => 'Strawberry Cupcakes',
                'short_description' => 'Fresh strawberry cupcakes',
                'description' => 'Delightful strawberry cupcakes with strawberry buttercream topping.',
                'regular_price' => 18.99,
                'sale_price' => 15.99,
                'sku' => 'CAKE-CUP-STR-004',
                'stock_quantity' => 24,
                'category_id' => 3, // Cupcakes
                'is_active' => true,
                'is_featured' => false,
                'sizes' => json_encode(['regular']),
                'flavors' => json_encode(['strawberry']),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
