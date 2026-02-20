<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('currency')) {
    /**
     * Format a number with the configured currency symbol.
     *
     * @param  float|int  $amount
     * @param  int        $decimals
     * @return string
     */
    function currency($amount, int $decimals = 2): string
    {
        $symbol = Setting::get('currency_symbol', '$');
        return $symbol . number_format((float) $amount, $decimals);
    }
}
