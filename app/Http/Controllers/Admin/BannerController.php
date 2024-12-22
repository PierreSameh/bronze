<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Get all banners
    public function index()
    {
        $banners = Banner::all();
        return response()->json([
            'success' => true,
            'data' => $banners,
        ], 200);
    }

    // Create a new banner
    public function store(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('path')) {
            $path = $request->file('path')->store('banners', 'public');
            $banner = Banner::create(['path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully.',
                'data' => $banner,
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to upload banner image.',
        ], 400);
    }

    // Get a single banner
    public function show($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $banner,
        ], 200);
    }

    // Update a banner
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found.',
            ], 404);
        }

        $validated = $request->validate([
            'active' => "required|in:0,1"
        ]);

        $banner->update([
            'active' => $request->active
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully.',
            'data' => $banner,
        ], 200);
    }

    // Delete a banner
    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found.',
            ], 404);
        }

        // Delete the file if it exists
        if ($banner->path && Storage::disk('public')->exists($banner->path)) {
            Storage::disk('public')->delete($banner->path);
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully.',
        ], 200);
    }


}
