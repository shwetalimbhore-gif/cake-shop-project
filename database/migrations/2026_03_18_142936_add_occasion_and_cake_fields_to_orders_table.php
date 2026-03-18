<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOccasionAndCakeFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add occasion field (missing)
            if (!Schema::hasColumn('orders', 'occasion')) {
                $table->string('occasion')->nullable()->after('notes');
            }

            // Add is_custom_cake field (missing)
            if (!Schema::hasColumn('orders', 'is_custom_cake')) {
                $table->boolean('is_custom_cake')->default(false)->after('order_type');
            }

            // Add custom_message field (missing)
            if (!Schema::hasColumn('orders', 'custom_message')) {
                $table->text('custom_message')->nullable()->after('is_custom_cake');
            }

            // Add cake_design field (missing)
            if (!Schema::hasColumn('orders', 'cake_design')) {
                $table->string('cake_design')->nullable()->after('custom_message');
            }

            // Add pre_order_date field (missing)
            if (!Schema::hasColumn('orders', 'pre_order_date')) {
                $table->date('pre_order_date')->nullable()->after('cake_design');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['occasion', 'is_custom_cake', 'custom_message', 'cake_design', 'pre_order_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
