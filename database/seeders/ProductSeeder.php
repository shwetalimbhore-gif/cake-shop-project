<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::first();

        if (!$category) {
            return;
        }

        Product::create([
            'category_id' => $category->id,
            'name' => 'Chocolate Truffle Cake',
            'slug' => 'chocolate-truffle-cake',
            'description' => 'Rich chocolate cake',
            'base_price' => 599,
            'is_active' => true,
        ]);
    }
}
