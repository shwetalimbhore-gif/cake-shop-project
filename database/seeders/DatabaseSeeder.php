<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    // ===== YOU ONLY NEED TO CHANGE THESE NUMBERS =====
    private $numberOfUsers = 500;
    private $numberOfCategories = 20;
    private $numberOfProducts = 200;
    private $numberOfOrders = 2000;
    private $numberOfReviews = 3000;
    // =================================================

    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->truncateTables();
        $this->seedSettings();
        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedOrders();
        $this->seedOrderItems();
        $this->seedReviews();
        $this->seedPayments();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->showSummary();
    }

    private function truncateTables(): void
    {
        $tables = ['payments', 'reviews', 'order_items', 'orders', 'products', 'categories', 'users', 'settings'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->info("✓ Truncated: {$table}");
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Cozy Cravings', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Delicious cakes for every occasion', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'info@cozycravings.com', 'type' => 'email', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+1 234 567 8900', 'type' => 'phone', 'group' => 'contact'],
            ['key' => 'delivery_charges', 'value' => '10.00', 'type' => 'number', 'group' => 'delivery'],
            ['key' => 'free_delivery_threshold', 'value' => '100.00', 'type' => 'number', 'group' => 'delivery'],
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'number', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '₹', 'type' => 'text', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
        $this->command->info("✓ Settings seeded");
    }

    private function seedUsers(): void
    {
        $this->command->info("📊 Creating {$this->numberOfUsers} users...");
        User::factory()->admin()->create();
        User::factory()->count($this->numberOfUsers - 1)->create();
        $this->command->info("✓ Users created: " . User::count());
    }

    private function seedCategories(): void
    {
        $this->command->info("📊 Creating {$this->numberOfCategories} categories...");
        Category::factory()->count($this->numberOfCategories)->create();
        $this->command->info("✓ Categories created: " . Category::count());
    }

    private function seedProducts(): void
    {
        $this->command->info("📊 Creating {$this->numberOfProducts} products...");
        Product::factory()->count($this->numberOfProducts)->create();
        $this->command->info("✓ Products created: " . Product::count());
    }

    private function seedOrders(): void
    {
        $this->command->info("📊 Creating {$this->numberOfOrders} orders...");

        $chunkSize = 200;
        for ($i = 0; $i < $this->numberOfOrders; $i += $chunkSize) {
            $count = min($chunkSize, $this->numberOfOrders - $i);
            Order::factory()->count($count)->create();
            $this->command->info("  Progress: " . min($i + $chunkSize, $this->numberOfOrders) . "/{$this->numberOfOrders}");
        }
        $this->command->info("✓ Orders created: " . Order::count());
    }

    private function seedOrderItems(): void
    {
        $this->command->info("📊 Creating order items...");
        $orders = Order::all();
        $products = Product::all();

        $totalItems = 0;
        foreach ($orders as $order) {
            $itemCount = rand(1, 5);
            for ($i = 0; $i < $itemCount; $i++) {
                OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $products->random()->id,
                ]);
                $totalItems++;
            }
        }
        $this->command->info("✓ Order items created: " . $totalItems);
    }

    private function seedReviews(): void
    {
        $this->command->info("📊 Creating {$this->numberOfReviews} reviews...");
        $products = Product::all();
        $users = User::where('is_admin', false)->get();

        for ($i = 0; $i < $this->numberOfReviews; $i += 100) {
            $count = min(100, $this->numberOfReviews - $i);
            Review::factory()->count($count)->create([
                'product_id' => $products->random()->id,
                'user_id' => $users->random()->id,
            ]);
        }
        $this->command->info("✓ Reviews created: " . Review::count());
    }

    private function seedPayments(): void
    {
        $this->command->info("📊 Creating payments...");
        $orders = Order::where('payment_status', 'paid')->get();

        foreach ($orders as $order) {
            Payment::factory()->create(['order_id' => $order->id]);
        }
        $this->command->info("✓ Payments created: " . Payment::count());
    }

    private function showSummary(): void
    {
        $this->command->info('');
        $this->command->info('✅ SEEDING COMPLETE!');
        $this->command->info('=================================');
        $this->command->info('Users: ' . User::count() . '/' . $this->numberOfUsers);
        $this->command->info('Categories: ' . Category::count() . '/' . $this->numberOfCategories);
        $this->command->info('Products: ' . Product::count() . '/' . $this->numberOfProducts);
        $this->command->info('Orders: ' . Order::count() . '/' . $this->numberOfOrders);
        $this->command->info('Reviews: ' . Review::count() . '/' . $this->numberOfReviews);
        $this->command->info('Payments: ' . Payment::count());
        $this->command->info('=================================');
        $this->command->info('💰 Total Revenue: ₹' . number_format(Order::sum('total'), 2));
        $this->command->info('🛒 Online Orders: ' . Order::where('order_type', 'online')->count());
        $this->command->info('🏪 Walk-in Orders: ' . Order::where('order_type', 'walkin')->count());
        $this->command->info('=================================');
        $this->command->info('🔑 Admin Login: admin@cakeshop.com / password');
    }
}
