<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Only add is_eggless if it doesn't exist
            if (!Schema::hasColumn('products', 'is_eggless')) {
                $table->boolean('is_eggless')->default(false)->after('is_featured');
            }

            // DO NOT add views again since it already exists
            // The views column is already present
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Only drop is_eggless if it exists
            if (Schema::hasColumn('products', 'is_eggless')) {
                $table->dropColumn('is_eggless');
            }
        });
    }
};
