<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, $default = null)
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }

    /**
     * Get all settings as an associative array.
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('app_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
