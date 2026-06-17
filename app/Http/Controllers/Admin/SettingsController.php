<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsRequest;
use App\Services\FileUploadService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    protected const GROUPS = ['general', 'smtp', 'sms', 'notifications', 'theme', 'branding'];

    public function __construct(
        protected SettingService $settings,
        protected FileUploadService $files,
    ) {
    }

    public function index(Request $request, string $group = 'general'): View
    {
        abort_unless($request->user()->can('settings.view'), 403);
        abort_unless(in_array($group, self::GROUPS), 404);

        return view('admin.settings.index', ['group' => $group, 'groups' => self::GROUPS]);
    }

    public function update(SettingsRequest $request, string $group): RedirectResponse
    {
        abort_unless($request->user()->can('settings.edit'), 403);
        abort_unless(in_array($group, self::GROUPS), 404);

        $validated = $request->validated();

        if ($group === 'branding') {
            foreach (['logo' => 'branding_logo', 'logo_dark' => 'branding_logo_dark', 'favicon' => 'branding_favicon'] as $field => $key) {
                if ($request->hasFile($field)) {
                    $this->settings->uploadLogo($this->files, $request->file($field), $key);
                }
            }
        } else {
            $this->settings->updateGroup($group, $validated);
        }

        return redirect()->route('admin.settings.index', $group)
            ->with('success', ucfirst($group).' settings saved.');
    }
}
