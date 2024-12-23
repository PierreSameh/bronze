<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    /**
     * Display a listing of the promocodes.
     */
    public function index()
    {
        $promocodes = Promocode::all();

        return response()->json([
            "success" => true,
            "message" => "Promocodes retrieved successfully",
            'promocodes' => $promocodes,
        ], 200);
    }
    public function paginate(Request $request)
    {
        $promocodes = Promocode::paginate((int) $request->per_page ?: 10);

        return response()->json([
            "success" => true,
            "message" => "Promocodes retrieved successfully",
            'promocodes' => $promocodes,
        ], 200);
    }

    /**
     * Store a newly created promocode in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promocode' => 'required|string|max:255|unique:promocodes,promocode',
            'percentage' => 'required|numeric|between:0,100',
        ]);

        $promocode = Promocode::create($request->only(['promocode', 'percentage']));

        return response()->json([
            "success" => true,
            'message' => 'Promocode created successfully',
            'promocode' => $promocode,
        ], 201);
    }

    /**
     * Display the specified promocode.
     */
    public function show($id)
    {
        $promocode = Promocode::find($id);

        if (!$promocode) {
            return response()->json(['message' => 'Promocode not found'], 404);
        }

        return response()->json([
            'promocode' => $promocode,
        ]);
    }

    /**
     * Update the specified promocode in storage.
     */
    public function update(Request $request, $id)
    {
        $promocode = Promocode::find($id);

        if (!$promocode) {
            return response()->json(['message' => 'Promocode not found'], 404);
        }

        $request->validate([
            'percentage' => 'sometimes|required|numeric|between:0,100',
            'status' => 'sometimes|required|in:active,suspended',
        ]);

        $promocode->update($request->only(['percentage', 'status']));

        return response()->json([
            'message' => 'Promocode updated successfully',
            'promocode' => $promocode,
        ]);
    }

    /**
     * Remove the specified promocode from storage.
     */
    public function destroy($id)
    {
        $promocode = Promocode::find($id);

        if (!$promocode) {
            return response()->json(['message' => 'Promocode not found'], 404);
        }

        $promocode->delete();

        return response()->json([
            'message' => 'Promocode deleted successfully',
        ]);
    }
}
