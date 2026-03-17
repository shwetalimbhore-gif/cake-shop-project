<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type')->default('online')->after('order_number');
            }

            if (!Schema::hasColumn('orders', 'walkin_customer_name')) {
                $table->string('walkin_customer_name')->nullable()->after('order_type');
            }

            if (!Schema::hasColumn('orders', 'walkin_customer_phone')) {
                $table->string('walkin_customer_phone')->nullable()->after('walkin_customer_name');
            }

            if (!Schema::hasColumn('orders', 'walkin_notes')) {
                $table->text('walkin_notes')->nullable()->after('walkin_customer_phone');
            }

            if (!Schema::hasColumn('orders', 'created_by_admin')) {
                $table->foreignId('created_by_admin')->nullable()->constrained('users')->after('walkin_notes');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'walkin_customer_name',
                'walkin_customer_phone',
                'walkin_notes',
                'created_by_admin'
            ]);
        });
    }
};
