<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add phone column if it doesn't exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            // Add address fields if they don't exist
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }

            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }

            if (!Schema::hasColumn('users', 'zip')) {
                $table->string('zip')->nullable()->after('state');
            }

            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('zip');
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['phone', 'address', 'city', 'state', 'zip', 'country', 'avatar'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
