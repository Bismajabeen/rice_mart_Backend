<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Get All Cities
     */
    public function index()
    {
        $cities = City::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Cities fetched successfully.',
            'data' => $cities,
        ], 200);
    }

    /**
     * Add New City
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name',
            'code' => 'nullable|string|max:20',
        ]);

        $city = City::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'City added successfully.',
            'data' => $city,
        ], 201);
    }

    /**
     * Update City
     */
    public function update(Request $request, $id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cities', 'name')->ignore($city->id),
            ],
            'code' => 'nullable|string|max:20',
        ]);

        $city->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'City updated successfully.',
            'data' => $city,
        ], 200);
    }

    /**
     * Delete City
     */
    public function destroy($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found.',
            ], 404);
        }

        // Prevent deleting city if courier charge exists
        if ($city->courierCharge()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This city has an assigned courier charge. Please delete the courier charge first.',
            ], 400);
        }

        $city->delete();

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully.',
        ], 200);
    }

    public function availableCities()
    {
       $cities = City::whereDoesntHave('courierCharge')
         ->orderBy('name')
         ->get();

       return response()->json([
         'success' => true,
         'data' => $cities,
       ], 200);
    }
}