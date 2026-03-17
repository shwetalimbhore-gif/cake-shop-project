<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $isWalkin = fake()->boolean(30);
        $createdAt = fake()->dateTimeBetween('-1 year', 'now');
        $daysOld = now()->diffInDays($createdAt);

        // Smart status based on order age
        $status = match(true) {
            $daysOld > 30 => fake()->randomElement(['delivered', 'cancelled']),
            $daysOld > 7 => fake()->randomElement(['delivered', 'shipped']),
            $daysOld > 2 => fake()->randomElement(['processing', 'confirmed', 'shipped']),
            default => fake()->randomElement(['pending', 'processing']),
        };

        $paymentStatus = match($status) {
            'delivered', 'shipped' => 'paid',
            'cancelled' => fake()->randomElement(['pending', 'refunded']),
            default => fake()->randomElement(['pending', 'paid']),
        };

        $subtotal = fake()->randomFloat(2, 20, 500);
        $tax = round($subtotal * 0.1, 2);
        $shipping = $isWalkin ? 0 : fake()->randomFloat(2, 0, 20);
        $discount = fake()->boolean(20) ? fake()->randomFloat(2, 5, 50) : 0;
        $total = round($subtotal + $tax + $shipping - $discount, 2);

        $name = fake()->name();
        $email = fake()->email();
        $phone = fake()->phoneNumber();

        return [
            'order_number' => 'ORD-' . strtoupper(fake()->unique()->bothify('###???')),
            'order_type' => $isWalkin ? 'walkin' : 'online',
            'user_id' => $isWalkin ? null : User::inRandomOrder()->first()?->id ?? User::factory(),
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => fake()->randomElement(['credit_card', 'RazorPay', 'cash_on_delivery', 'upi']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'shipping_name' => $name,
            'shipping_email' => $email,
            'shipping_phone' => $phone,
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->stateAbbr(),
            'shipping_zip' => fake()->postcode(),
            'shipping_country' => fake()->country(),
            'billing_name' => $name,
            'billing_email' => $email,
            'billing_phone' => $phone,
            'billing_address' => fake()->streetAddress(),
            'billing_city' => fake()->city(),
            'billing_state' => fake()->stateAbbr(),
            'billing_zip' => fake()->postcode(),
            'billing_country' => fake()->country(),
            'walkin_customer_name' => $isWalkin ? $name : null,
            'walkin_customer_phone' => $isWalkin ? $phone : null,
            'walkin_notes' => $isWalkin && fake()->boolean(50) ? fake()->sentence() : null,
            'created_by_admin' => $isWalkin ? User::where('is_admin', true)->first()?->id : null,
            'notes' => fake()->boolean(30) ? fake()->paragraph() : null,
            'tracking_number' => in_array($status, ['shipped', 'delivered']) ? strtoupper(fake()->bothify('TRK###???')) : null,
            'shipped_at' => in_array($status, ['shipped', 'delivered']) ? fake()->dateTimeBetween($createdAt, '+7 days') : null,
            'delivered_at' => $status === 'delivered' ? fake()->dateTimeBetween('+7 days', '+14 days') : null,
            'cancelled_at' => $status === 'cancelled' ? fake()->dateTimeBetween($createdAt, '+5 days') : null,
            'created_at' => $createdAt,
            'updated_at' => fn($attr) => fake()->dateTimeBetween($attr['created_at'], 'now'),
        ];
    }
}
