<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $order = Order::where('payment_status', 'paid')->inRandomOrder()->first() ?? Order::factory();

        return [
            'razorpay_payment_id' => 'pay_' . fake()->regexify('[A-Za-z0-9]{20}'),
            'razorpay_order_id' => 'order_' . fake()->regexify('[A-Za-z0-9]{20}'),
            'razorpay_signature' => fake()->regexify('[A-Za-z0-9]{50}'),
            'order_id' => $order->id,
            'method' => fake()->randomElement(['card', 'upi', 'netbanking']),
            'currency' => 'INR',
            'amount' => $order->total,
            'status' => 'success',
            'json_response' => json_encode(['id' => fake()->uuid()]),
            'created_at' => $order->created_at,
            'updated_at' => $order->created_at,
        ];
    }
}
