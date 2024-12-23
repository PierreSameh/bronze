<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return response()->json($settings, 200);
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'distinguishes_en' => 'nullable|string',
            'distinguishes_ar' => 'nullable|string',
            'privacy_en' => 'nullable|string',
            'privacy_ar' => 'nullable|string',
            'terms_en' => 'nullable|string',
            'terms_ar' => 'nullable|string',
            'roles_en' => 'nullable|string',
            'roles_ar' => 'nullable|string',
            'about_en' => 'nullable|string',
            'about_ar' => 'nullable|string',
            'services_en' => 'nullable|string',
            'services_ar' => 'nullable|string',
        ]);

        $settings = Setting::first();

        if (!$settings) {
            return response()->json(['message' => 'Settings not found.'], 404);
        }

        $settings->update($request->all());

        return response()->json(['message' => 'Settings updated successfully.', 'settings' => $settings], 200);
    }
}
