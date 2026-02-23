<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add only the missing fields
            if (!Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('tracking_number');
            }

            if (!Schema::hasColumn('orders', 'estimated_delivery')) {
                $table->timestamp('estimated_delivery')->nullable()->after('courier_name');
            }

            if (!Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('estimated_delivery');
            }

            if (!Schema::hasColumn('orders', 'tracking_history')) {
                $table->json('tracking_history')->nullable()->after('delivery_notes');
            }

            if (!Schema::hasColumn('orders', 'current_location')) {
                $table->string('current_location')->nullable()->after('tracking_history');
            }

            if (!Schema::hasColumn('orders', 'driver_latitude')) {
                $table->decimal('driver_latitude', 10, 8)->nullable()->after('current_location');
            }

            if (!Schema::hasColumn('orders', 'driver_longitude')) {
                $table->decimal('driver_longitude', 11, 8)->nullable()->after('driver_latitude');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'courier_name',
                'estimated_delivery',
                'delivery_notes',
                'tracking_history',
                'current_location',
                'driver_latitude',
                'driver_longitude'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
