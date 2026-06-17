<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

if (! function_exists('setting')) {
    /**
     * Get an application setting value by key with cache-backed lookup.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            $settings = Cache::rememberForever('app_settings', function () {
                if (! Schema::hasTable('settings')) {
                    return [];
                }

                return Setting::query()->pluck('value', 'key')->all();
            });
        } catch (\Throwable) {
            return $default;
        }

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('forget_settings_cache')) {
    function forget_settings_cache(): void
    {
        Cache::forget('app_settings');
    }
}

if (! function_exists('active_menu')) {
    /**
     * Return "active" when the current route matches any given pattern.
     */
    function active_menu(string|array $patterns, string $class = 'active'): string
    {
        return request()->routeIs($patterns) ? $class : '';
    }
}

if (! function_exists('status_badge')) {
    /**
     * Render a Bootstrap status badge for an active/inactive flag.
     */
    function status_badge(bool|int $active): string
    {
        return $active
            ? '<span class="badge rounded-pill text-bg-success-subtle badge-status">Active</span>'
            : '<span class="badge rounded-pill text-bg-danger-subtle badge-status">Inactive</span>';
    }
}
