<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        try {
            $settings = Cache::remember('site_settings', 3600, function () {
                return Setting::pluck('value', 'key')->toArray();
            });

            return $settings[$key] ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('logoSetting')) {
    function logoSetting($key, $default = null)
    {
        try {
            $settings = Cache::remember('site_settings', 3600, function () {
                return Setting::pluck('value', 'key')->toArray();
            });

            return $settings[$key] ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('site_logo')) {
    function site_logo()
    {
        $logo = setting('site_logo');
        return $logo ? asset('storage/' . $logo) : asset('images/default-logo.png');
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        $symbol = setting('currency_symbol', '$');
        return $symbol . number_format($amount, 2);
    }
}
