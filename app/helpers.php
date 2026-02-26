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
     * Drops trailing zeros (e.g., $100 instead of $100.00).
     *
     * @param  float|int  $amount
     * @param  int        $decimals
     * @return string
     */
    function currency($amount, int $decimals = 2): string
    {
        $symbol = Setting::get('currency_symbol', '$');
        return $symbol . format_number($amount);
    }
}

if (!function_exists('format_number')) {
    /**
     * Format a number allowing up to 2 decimal places, stripped of unnecessary trailing zeroes.
     *
     * @param  mixed  $value
     * @return string
     */
    function format_number($value): string
    {
        if (!is_numeric($value)) {
            return $value ?? '0';
        }

        $value = (float) $value;
        $value = round($value, 2);

        $formatted = number_format($value, 2, '.', ',');

        if (strpos($formatted, '.') !== false) {
            $formatted = rtrim($formatted, '0');
            $formatted = rtrim($formatted, '.');
        }

        return $formatted;
    }
}
