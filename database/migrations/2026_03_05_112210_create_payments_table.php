<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('method')->nullable();
            $table->string('currency')->default('INR');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->longText('json_response')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
