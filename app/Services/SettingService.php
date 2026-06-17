<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Persist a group of settings from a validated key/value map.
     */
    public function updateGroup(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::set($key, $value, $group);
        }
    }

    public function uploadLogo(FileUploadService $files, $file, string $key): void
    {
        $old = setting($key);
        $path = $files->replace($file, 'branding', $old);

        Setting::set($key, $path, 'branding');
    }
}
