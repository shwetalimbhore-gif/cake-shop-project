<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Ensure all pricing columns exist
            if (!Schema::hasColumn('products', 'base_price')) {
                $table->decimal('base_price', 10, 2)->nullable()->after('sale_price');
            }

            if (!Schema::hasColumn('products', 'size_prices')) {
                $table->json('size_prices')->nullable()->after('sizes');
            }

            if (!Schema::hasColumn('products', 'flavor_prices')) {
                $table->json('flavor_prices')->nullable()->after('flavors');
            }

            if (!Schema::hasColumn('products', 'has_custom_options')) {
                $table->boolean('has_custom_options')->default(false)->after('flavor_prices');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'size_prices', 'flavor_prices', 'has_custom_options']);
        });
    }
};
