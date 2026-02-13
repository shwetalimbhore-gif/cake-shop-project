<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call Settings Seeder first
        $this->call(SettingsSeeder::class);

        // Create admin user only if not exists
        User::firstOrCreate(
            ['email' => 'admin@cakeshop.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create regular user only if not exists
        User::firstOrCreate(
            ['email' => 'customer@cakeshop.com'],
            [
                'name' => 'Customer',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Create categories only if table is empty
        if (Category::count() === 0) {
            $categories = [
                ['name' => 'Birthday Cakes', 'description' => 'Perfect for birthdays', 'is_active' => true, 'order' => 1],
                ['name' => 'Wedding Cakes', 'description' => 'Elegant wedding cakes', 'is_active' => true, 'order' => 2],
                ['name' => 'Cupcakes', 'description' => 'Delicious bite-sized treats', 'is_active' => true, 'order' => 3],
                ['name' => 'Custom Cakes', 'description' => 'Designed just for you', 'is_active' => true, 'order' => 4],
            ];

            foreach ($categories as $category) {
                Category::create($category);
            }
            $this->command->info('Categories seeded successfully!');
        } else {
            $this->command->info('Categories table already has data. Skipping...');
        }

        // Create sample products only if table is empty
        if (Product::count() === 0) {
            $products = [
                [
                    'name' => 'Chocolate Cake',
                    'short_description' => 'Rich and moist chocolate cake',
                    'description' => 'Our signature chocolate cake with creamy chocolate frosting.',
                    'regular_price' => 29.99,
                    'sale_price' => 24.99,
                    'sku' => 'CAKE-BDAY-CHOC-001',
                    'stock_quantity' => 15,
                    'category_id' => 1,
                    'is_active' => true,
                    'is_featured' => true,
                    'sizes' => json_encode(['Small (6")', 'Medium (8")', 'Large (10")']),
                    'flavors' => json_encode(['Chocolate', 'Double Chocolate']),
                ],
                [
                    'name' => 'Vanilla Birthday Cake',
                    'short_description' => 'Classic vanilla cake',
                    'description' => 'Light and fluffy vanilla cake with buttercream frosting.',
                    'regular_price' => 27.99,
                    'sale_price' => null,
                    'sku' => 'CAKE-BDAY-VAN-002',
                    'stock_quantity' => 10,
                    'category_id' => 1,
                    'is_active' => true,
                    'is_featured' => false,
                    'sizes' => json_encode(['Small (6")', 'Medium (8")', 'Large (10")']),
                    'flavors' => json_encode(['Vanilla', 'Vanilla Bean']),
                ],
                [
                    'name' => 'Red Velvet Cake',
                    'short_description' => 'Classic red velvet with cream cheese frosting',
                    'description' => 'Stunning red velvet cake with rich cream cheese frosting.',
                    'regular_price' => 32.99,
                    'sale_price' => 28.99,
                    'sku' => 'CAKE-BDAY-RED-003',
                    'stock_quantity' => 8,
                    'category_id' => 1,
                    'is_active' => true,
                    'is_featured' => true,
                    'sizes' => json_encode(['Medium (8")', 'Large (10")']),
                    'flavors' => json_encode(['Red Velvet']),
                ],
                [
                    'name' => 'Classic White Wedding Cake',
                    'short_description' => 'Elegant three-tier wedding cake',
                    'description' => 'Beautiful three-tier white wedding cake.',
                    'regular_price' => 299.99,
                    'sale_price' => 279.99,
                    'sku' => 'CAKE-WED-CLS-001',
                    'stock_quantity' => 3,
                    'category_id' => 2,
                    'is_active' => true,
                    'is_featured' => true,
                    'sizes' => json_encode(['3-Tier (50 servings)', '4-Tier (80 servings)']),
                    'flavors' => json_encode(['Vanilla', 'Almond']),
                ],
                [
                    'name' => 'Strawberry Cupcakes',
                    'short_description' => 'Fresh strawberry cupcakes',
                    'description' => 'Delightful strawberry cupcakes with strawberry buttercream.',
                    'regular_price' => 18.99,
                    'sale_price' => 15.99,
                    'sku' => 'CAKE-CUP-STR-001',
                    'stock_quantity' => 24,
                    'category_id' => 3,
                    'is_active' => true,
                    'is_featured' => false,
                    'sizes' => json_encode(['Regular', 'Mini']),
                    'flavors' => json_encode(['Strawberry', 'Strawberry Cheesecake']),
                ],
                [
                    'name' => 'Custom Design Cake',
                    'short_description' => 'Fully customizable cake',
                    'description' => 'Create your own custom cake design.',
                    'regular_price' => 89.99,
                    'sale_price' => null,
                    'sku' => 'CAKE-CUS-DES-001',
                    'stock_quantity' => 5,
                    'category_id' => 4,
                    'is_active' => true,
                    'is_featured' => true,
                    'sizes' => json_encode(['6"', '8"', '10"', 'Quarter Sheet', 'Half Sheet']),
                    'flavors' => json_encode(['Vanilla', 'Chocolate', 'Red Velvet', 'Lemon']),
                ],
            ];

            foreach ($products as $product) {
                Product::create($product);
            }
            $this->command->info('Products seeded successfully!');
        } else {
            $this->command->info('Products table already has data. Skipping...');
        }

        // Get customer for orders
        $customer = User::where('email', 'customer@cakeshop.com')->first();
        $products = Product::all();

        // Create orders only if table is empty
        if ($customer && $products->count() > 0 && Order::count() === 0) {
            $statuses = ['pending', 'processing', 'confirmed', 'shipped', 'delivered', 'cancelled'];
            $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

            for ($i = 1; $i <= 15; $i++) {
                $status = $statuses[array_rand($statuses)];
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

                $subtotal = rand(50, 300);
                $tax = round($subtotal * 0.1, 2);
                $shipping = 10.00;
                $discount = ($i % 3 == 0) ? rand(5, 20) : 0;
                $total = $subtotal + $tax + $shipping - $discount;

                $order = Order::create([
                    'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i + 100, 4, '0', STR_PAD_LEFT),
                    'user_id' => $customer->id,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => ['credit_card', 'paypal', 'cash_on_delivery'][array_rand(['credit_card', 'paypal', 'cash_on_delivery'])],
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping_cost' => $shipping,
                    'discount' => $discount,
                    'total' => $total,
                    'shipping_name' => $customer->name,
                    'shipping_email' => $customer->email,
                    'shipping_phone' => '555-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'shipping_address' => rand(100, 999) . ' Main Street',
                    'shipping_city' => 'New York',
                    'shipping_state' => 'NY',
                    'shipping_zip' => rand(10000, 99999),
                    'shipping_country' => 'USA',
                    'billing_name' => $customer->name,
                    'billing_email' => $customer->email,
                    'billing_phone' => '555-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'billing_address' => rand(100, 999) . ' Main Street',
                    'billing_city' => 'New York',
                    'billing_state' => 'NY',
                    'billing_zip' => rand(10000, 99999),
                    'billing_country' => 'USA',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                $numItems = rand(1, 3);
                $selectedProducts = $products->random(min($numItems, $products->count()));

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $quantity,
                        'price' => $product->regular_price,
                        'subtotal' => $product->regular_price * $quantity,
                        'options' => json_encode(['size' => 'Medium', 'flavor' => 'Vanilla']),
                        'created_at' => $order->created_at,
                    ]);
                }
            }
            $this->command->info('Orders seeded successfully!');
        } else {
            $this->command->info('Orders table already has data. Skipping...');
        }

        $this->command->info('Database seeding completed successfully!');
    }
}
