<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        // ============= CATEGORIES =============
        $categories = [
            [
                'name' => 'Birthday Cakes',
                'description' => 'Celebrate another year with our delicious birthday cakes',
                'image' => null,
                'is_active' => true,
                'order' => 1
            ],
            [
                'name' => 'Wedding Cakes',
                'description' => 'Elegant cakes for your special day',
                'image' => null,
                'is_active' => true,
                'order' => 2
            ],
            [
                'name' => 'Cupcakes',
                'description' => 'Perfect bite-sized treats for any occasion',
                'image' => null,
                'is_active' => true,
                'order' => 3
            ],
            [
                'name' => 'Custom Cakes',
                'description' => 'Bring your dream cake to life',
                'image' => null,
                'is_active' => true,
                'order' => 4
            ],
            [
                'name' => 'Anniversary Cakes',
                'description' => 'Celebrate your love with a beautiful cake',
                'image' => null,
                'is_active' => true,
                'order' => 5
            ],
            [
                'name' => 'Baby Shower Cakes',
                'description' => 'Welcome the little one with a sweet treat',
                'image' => null,
                'is_active' => true,
                'order' => 6
            ],
            [
                'name' => 'Christmas Cakes',
                'description' => 'Festive flavors for the holiday season',
                'image' => null,
                'is_active' => true,
                'order' => 7
            ],
            [
                'name' => 'Eggless Special',
                'description' => 'Delicious cakes made without eggs',
                'image' => null,
                'is_active' => true,
                'order' => 8
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        $this->command->info('Categories created successfully!');

        // ============= PRODUCTS =============
        $products = [
            // Birthday Cakes (Category ID: 1)
            [
                'name' => 'Classic Chocolate Cake',
                'short_description' => 'Rich and moist chocolate cake with chocolate ganache',
                'description' => 'Our signature chocolate cake made with premium Belgian chocolate. Layers of moist chocolate sponge filled with chocolate buttercream and covered in smooth chocolate ganache. Perfect for chocolate lovers!',
                'regular_price' => 45.00,
                'sale_price' => 39.99,
                'sku' => 'CAKE-BDAY-CHOC-001',
                'stock_quantity' => 15,
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['6 inch (6-8 servings)', '8 inch (10-12 servings)', '10 inch (14-16 servings)']),
                'flavors' => json_encode(['Chocolate', 'Double Chocolate']),
                'views' => 0,
            ],
            [
                'name' => 'Red Velvet Dream',
                'short_description' => 'Classic red velvet with cream cheese frosting',
                'description' => 'Stunning red velvet cake with a hint of cocoa, layered with rich cream cheese frosting. A timeless favorite that tastes as good as it looks!',
                'regular_price' => 52.00,
                'sale_price' => 44.99,
                'sku' => 'CAKE-BDAY-RED-002',
                'stock_quantity' => 12,
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['6 inch', '8 inch', '10 inch']),
                'flavors' => json_encode(['Red Velvet']),
                'views' => 0,
            ],
            [
                'name' => 'Vanilla Bean Celebration',
                'short_description' => 'Light and fluffy vanilla cake with buttercream',
                'description' => 'Classic vanilla cake made with real Madagascar vanilla beans. Light, fluffy, and perfectly sweetened, topped with silky vanilla buttercream. Perfect for any celebration!',
                'regular_price' => 42.00,
                'sale_price' => null,
                'sku' => 'CAKE-BDAY-VAN-003',
                'stock_quantity' => 20,
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => true,
                'sizes' => json_encode(['6 inch', '8 inch', '10 inch']),
                'flavors' => json_encode(['Vanilla']),
                'views' => 0,
            ],

            // Wedding Cakes (Category ID: 2)
            [
                'name' => 'Elegant White Wedding Cake',
                'short_description' => 'Classic three-tier wedding cake',
                'description' => 'Beautiful three-tier wedding cake with vanilla buttercream and delicate piping. Serves 50-60 guests. Customizable with fresh flowers.',
                'regular_price' => 299.99,
                'sale_price' => 279.99,
                'sku' => 'CAKE-WED-CLS-001',
                'stock_quantity' => 3,
                'category_id' => 2,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['3-Tier (50 servings)', '4-Tier (80 servings)']),
                'flavors' => json_encode(['Vanilla', 'Almond', 'Lemon']),
                'views' => 0,
            ],
            [
                'name' => 'Rustic Naked Wedding Cake',
                'short_description' => 'Trendy naked cake with fresh berries',
                'description' => 'Rustic style naked cake with layers of vanilla sponge, fresh berries, and light buttercream. Perfect for outdoor or barn weddings.',
                'regular_price' => 249.99,
                'sale_price' => null,
                'sku' => 'CAKE-WED-RUS-002',
                'stock_quantity' => 2,
                'category_id' => 2,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => true,
                'sizes' => json_encode(['2-Tier (30 servings)', '3-Tier (60 servings)']),
                'flavors' => json_encode(['Vanilla', 'Chocolate', 'Red Velvet']),
                'views' => 0,
            ],

            // Cupcakes (Category ID: 3)
            [
                'name' => 'Strawberry Delight Cupcakes',
                'short_description' => 'Fresh strawberry cupcakes with strawberry buttercream',
                'description' => 'Delightful strawberry cupcakes made with real strawberries, topped with strawberry buttercream and a fresh strawberry slice.',
                'regular_price' => 24.99,
                'sale_price' => 19.99,
                'sku' => 'CUP-STR-001',
                'stock_quantity' => 24,
                'category_id' => 3,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['Regular (Pack of 6)', 'Regular (Pack of 12)']),
                'flavors' => json_encode(['Strawberry']),
                'views' => 0,
            ],
            [
                'name' => 'Chocolate Truffle Cupcakes',
                'short_description' => 'Decadent chocolate cupcakes with ganache filling',
                'description' => 'Rich chocolate cupcakes filled with chocolate truffle ganache, topped with chocolate buttercream and chocolate shavings.',
                'regular_price' => 27.99,
                'sale_price' => null,
                'sku' => 'CUP-CHOC-002',
                'stock_quantity' => 30,
                'category_id' => 3,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['Regular (Pack of 6)', 'Regular (Pack of 12)']),
                'flavors' => json_encode(['Chocolate']),
                'views' => 0,
            ],
            [
                'name' => 'Vanilla Sprinkle Party',
                'short_description' => 'Fun vanilla cupcakes with rainbow sprinkles',
                'description' => 'Classic vanilla cupcakes topped with vanilla buttercream and rainbow sprinkles. Perfect for kids parties!',
                'regular_price' => 22.99,
                'sale_price' => 18.99,
                'sku' => 'CUP-VAN-003',
                'stock_quantity' => 25,
                'category_id' => 3,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => true,
                'sizes' => json_encode(['Regular (Pack of 6)', 'Regular (Pack of 12)']),
                'flavors' => json_encode(['Vanilla']),
                'views' => 0,
            ],

            // Custom Cakes (Category ID: 4)
            [
                'name' => 'Design Your Own Cake',
                'short_description' => 'Fully customizable cake - you choose everything!',
                'description' => 'Create your dream cake! Choose size, flavor, filling, frosting, and design. Our artists will bring your vision to life. Consultation included.',
                'regular_price' => 89.99,
                'sale_price' => null,
                'sku' => 'CUS-DES-001',
                'stock_quantity' => 10,
                'category_id' => 4,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['6 inch', '8 inch', '10 inch', 'Quarter Sheet', 'Half Sheet']),
                'flavors' => json_encode(['Vanilla', 'Chocolate', 'Red Velvet', 'Lemon', 'Marble', 'Carrot']),
                'views' => 0,
            ],

            // Anniversary Cakes (Category ID: 5)
            [
                'name' => 'Golden Anniversary Cake',
                'short_description' => 'Elegant cake with gold accents',
                'description' => 'Beautiful cake for 50th anniversary celebrations. Can be customized with your year number and message.',
                'regular_price' => 75.99,
                'sale_price' => 69.99,
                'sku' => 'CAKE-ANN-GLD-001',
                'stock_quantity' => 5,
                'category_id' => 5,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => false,
                'sizes' => json_encode(['6 inch', '8 inch', '10 inch']),
                'flavors' => json_encode(['Vanilla', 'Chocolate', 'Red Velvet']),
                'views' => 0,
            ],

            // Baby Shower Cakes (Category ID: 6)
            [
                'name' => 'Baby Boy Blue',
                'short_description' => 'Adorable blue cake for baby boy shower',
                'description' => 'Cute blue cake with baby-themed decorations. Perfect for welcoming a baby boy.',
                'regular_price' => 65.99,
                'sale_price' => 59.99,
                'sku' => 'BABY-BOY-001',
                'stock_quantity' => 6,
                'category_id' => 6,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => true,
                'sizes' => json_encode(['6 inch', '8 inch']),
                'flavors' => json_encode(['Vanilla', 'Strawberry']),
                'views' => 0,
            ],
            [
                'name' => 'Baby Girl Pink',
                'short_description' => 'Sweet pink cake for baby girl shower',
                'description' => 'Lovely pink cake with floral and baby-themed decorations. Perfect for welcoming a baby girl.',
                'regular_price' => 65.99,
                'sale_price' => 59.99,
                'sku' => 'BABY-GIRL-002',
                'stock_quantity' => 6,
                'category_id' => 6,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['6 inch', '8 inch']),
                'flavors' => json_encode(['Vanilla', 'Raspberry']),
                'views' => 0,
            ],

            // Christmas Cakes (Category ID: 7)
            [
                'name' => 'Christmas Fruit Cake',
                'short_description' => 'Traditional rich fruit cake with nuts and spices',
                'description' => 'Traditional Christmas fruit cake loaded with dried fruits, nuts, and warm spices. Soaked in rum for extra richness.',
                'regular_price' => 55.99,
                'sale_price' => 49.99,
                'sku' => 'XMAS-FRUIT-001',
                'stock_quantity' => 8,
                'category_id' => 7,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => false,
                'sizes' => json_encode(['1 lb', '2 lb', '3 lb']),
                'flavors' => json_encode(['Fruit Cake']),
                'views' => 0,
            ],
            [
                'name' => 'Peppermint Chocolate Cake',
                'short_description' => 'Rich chocolate with peppermint frosting',
                'description' => 'Decadent chocolate cake with refreshing peppermint buttercream and crushed candy cane topping.',
                'regular_price' => 48.99,
                'sale_price' => null,
                'sku' => 'XMAS-PEP-002',
                'stock_quantity' => 10,
                'category_id' => 7,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => false,
                'sizes' => json_encode(['6 inch', '8 inch', '10 inch']),
                'flavors' => json_encode(['Chocolate Peppermint']),
                'views' => 0,
            ],

            // Eggless Special (Category ID: 8)
            [
                'name' => 'Eggless Chocolate Fudge Cake',
                'short_description' => 'Rich chocolate cake made without eggs',
                'description' => 'Our special eggless chocolate cake that is just as moist and delicious as the original. Perfect for those with egg allergies or dietary restrictions.',
                'regular_price' => 46.99,
                'sale_price' => 41.99,
                'sku' => 'EGG-CHOC-001',
                'stock_quantity' => 12,
                'category_id' => 8,
                'is_active' => true,
                'is_featured' => true,
                'is_eggless' => true,
                'sizes' => json_encode(['6 inch', '8 inch', '10 inch']),
                'flavors' => json_encode(['Chocolate']),
                'views' => 0,
            ],
            [
                'name' => 'Eggless Vanilla Pound Cake',
                'short_description' => 'Classic pound cake made eggless',
                'description' => 'Traditional pound cake texture and taste, made completely without eggs. Pairs perfectly with tea or coffee.',
                'regular_price' => 38.99,
                'sale_price' => 34.99,
                'sku' => 'EGG-VAN-002',
                'stock_quantity' => 15,
                'category_id' => 8,
                'is_active' => true,
                'is_featured' => false,
                'is_eggless' => true,
                'sizes' => json_encode(['6 inch', '8 inch']),
                'flavors' => json_encode(['Vanilla', 'Lemon']),
                'views' => 0,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        $this->command->info('Products created successfully!');
    }
}
