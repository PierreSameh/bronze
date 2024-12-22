<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::with(['products.options', 'products.images'])->get();
        return response()->json(['categories' => $categories], 200);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',  // Image validation
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',  // Image validation
        ]);

        // Handle icon upload if present
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('public/categories');
            $validatedData['icon'] = Storage::url($iconPath);
        }

        // Handle cover upload if present
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('public/categories');
            $validatedData['cover'] = Storage::url($coverPath);
        }

        // Create the category
        $category = Category::create($validatedData);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }
    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $category = Category::with(['products.options', 'products.images'])->find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(['category' => $category], 200);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Validate the incoming request
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Handle icon upload if present
        if ($request->hasFile('icon')) {
            // Delete the old icon image if it exists
            if ($category->icon && Storage::exists(parse_url($category->icon, PHP_URL_PATH))) {
                Storage::delete(parse_url($category->icon, PHP_URL_PATH));
            }
            $iconPath = $request->file('icon')->store('public/categories');
            $validatedData['icon'] = Storage::url($iconPath);
        }

        // Handle cover upload if present
        if ($request->hasFile('cover')) {
            // Delete the old cover image if it exists
            if ($category->cover && Storage::exists(parse_url($category->cover, PHP_URL_PATH))) {
                Storage::delete(parse_url($category->cover, PHP_URL_PATH));
            }
            $coverPath = $request->file('cover')->store('public/categories');
            $validatedData['cover'] = Storage::url($coverPath);
        }

        // Update the category
        $category->update($validatedData);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

}
