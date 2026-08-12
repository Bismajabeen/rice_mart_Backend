<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourierCharge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourierChargeController extends Controller
{
    /**
     * Get All Courier Charges
     */
    public function index()
    {
        $charges = CourierCharge::with('city')
            ->join('cities', 'courier_charges.city_id', '=', 'cities.id')
            ->orderBy('cities.name')
            ->select('courier_charges.*')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Courier charges fetched successfully.',
            'data' => $charges,
        ], 200);
    }

    /**
     * Add Courier Charge
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id|unique:courier_charges,city_id',
            'charge' => 'required|numeric|min:1',
        ]);

        $charge = CourierCharge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Courier charge added successfully.',
            'data' => $charge->load('city'),
        ], 201);
    }

    /**
     * Update Courier Charge
     */
    public function update(Request $request, $id)
    {
        $courierCharge = CourierCharge::find($id);

        if (!$courierCharge) {
            return response()->json([
                'success' => false,
                'message' => 'Courier charge not found.',
            ], 404);
        }

        $validated = $request->validate([
            'city_id' => [
                'required',
                'exists:cities,id',
                Rule::unique('courier_charges', 'city_id')
                    ->ignore($courierCharge->id),
            ],
            'charge' => 'required|numeric|min:0',
        ]);

        $courierCharge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Courier charge updated successfully.',
            'data' => $courierCharge->load('city'),
        ], 200);
    }

    /**
     * Delete Courier Charge
     */
    public function destroy($id)
    {
        $courierCharge = CourierCharge::find($id);

        if (!$courierCharge) {
            return response()->json([
                'success' => false,
                'message' => 'Courier charge not found.',
            ], 404);
        }

        $courierCharge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Courier charge deleted successfully.',
        ], 200);
    }

    // CourierChargeController.php — new method
    public function deliverableCities()
    {
        $charges = CourierCharge::with('city')
         ->join('cities', 'courier_charges.city_id', '=', 'cities.id')
         ->orderBy('cities.name')
         ->select('courier_charges.*')
         ->get();

       return response()->json([
         'success' => true,
         'data' => $charges, // each item has ->city and ->charge
        ]);
    }
}