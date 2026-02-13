<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'MyCakeShop', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'image', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Delicious cakes for every occasion', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'site_keywords', 'value' => 'cakes, birthday cakes, wedding cakes, cupcakes', 'type' => 'text', 'group' => 'general'],

            // Contact Information
            ['key' => 'contact_email', 'value' => 'info@mycakeshop.com', 'type' => 'email', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+1 234 567 8900', 'type' => 'phone', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => '123 Bakery Street, Sweet City, SC 12345', 'type' => 'textarea', 'group' => 'contact'],
            ['key' => 'contact_map', 'value' => null, 'type' => 'text', 'group' => 'contact'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/mycakeshop', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/mycakeshop', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/mycakeshop', 'type' => 'text', 'group' => 'contact'],

            // Delivery Settings
            ['key' => 'delivery_charges', 'value' => '10.00', 'type' => 'number', 'group' => 'delivery'],
            ['key' => 'free_delivery_threshold', 'value' => '100.00', 'type' => 'number', 'group' => 'delivery'],
            ['key' => 'delivery_radius', 'value' => '20', 'type' => 'number', 'group' => 'delivery'],
            ['key' => 'delivery_time_slots', 'value' => json_encode([
                '09:00-12:00', '12:00-15:00', '15:00-18:00', '18:00-21:00'
            ]), 'type' => 'json', 'group' => 'delivery'],
            ['key' => 'delivery_days', 'value' => json_encode([
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
            ]), 'type' => 'json', 'group' => 'delivery'],

            // Opening Hours
            ['key' => 'opening_hours', 'value' => json_encode([
                'Monday' => '09:00-18:00',
                'Tuesday' => '09:00-18:00',
                'Wednesday' => '09:00-18:00',
                'Thursday' => '09:00-18:00',
                'Friday' => '09:00-20:00',
                'Saturday' => '10:00-16:00',
                'Sunday' => 'Closed'
            ]), 'type' => 'json', 'group' => 'hours'],

            // Advanced Settings
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'number', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'USD', 'type' => 'text', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'text', 'group' => 'general'],
            ['key' => 'order_prefix', 'value' => 'ORD-', 'type' => 'text', 'group' => 'general'],
            ['key' => 'enable_reviews', 'value' => '1', 'type' => 'boolean', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
