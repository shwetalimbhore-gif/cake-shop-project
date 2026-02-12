<?php
// database/migrations/2026_02_03_121412_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description');

            // Pricing
            $table->decimal('regular_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();

            // Inventory
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('in_stock')->default(true);

            // Category
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            // Images
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();

            // Cake Options
            $table->json('sizes')->nullable();
            $table->json('flavors')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Stats
            $table->integer('views')->default(0);

            $table->timestamps();

            // Indexes for faster queries
            $table->index('sku');
            $table->index('slug');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
