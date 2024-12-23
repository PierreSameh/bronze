<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index()
    {
        $socialLinks = SocialLink::first();

        if (!$socialLinks) {
            return response()->json(['message' => 'No social links found.'], 404);
        }

        return response()->json(['social_links' => $socialLinks], 200);
    }

    public function update(Request $request)
    {
        $socialLinks = SocialLink::first();

        if (!$socialLinks) {
            return response()->json(['message' => 'No social links found to update.'], 404);
        }

        $validatedData = $request->validate([
            'youtube' => 'nullable|url',
            'facebook' => 'nullable|url',
            'pinterest' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'email' => 'nullable|email',
        ]);

        $socialLinks->update($validatedData);

        return response()->json(['message' => 'Social links updated successfully.', 'social_links' => $socialLinks], 200);
    }
}

