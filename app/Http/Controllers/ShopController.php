<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\RiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    // ── Create Shop ───────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cnic_number'          => 'required|string|regex:/^\d{5}-\d{7}-\d{1}$/',
            'cnic_image'           => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'shop_name'            => 'required|string|max:255',
            'owner_name'           => 'required|string|max:255',
            'phone'                => 'required|string|max:20',
            'address'              => 'required|string|max:500',
            'description'          => 'nullable|string|max:1000',
            'rice_categories'      => 'required|string', // JSON string from Flutter
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        // ── Check if user already has a shop ──────────────────────────────
        if (Shop::where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a shop.',
            ], 409);
        }

        // ── Upload CNIC image ─────────────────────────────────────────────
        $cnicPath = $request->file('cnic_image')
            ->store('cnic_images', 'public');

        // ── Create shop ───────────────────────────────────────────────────
        $shop = Shop::create([
            'user_id'     => $user->id,
            'cnic_number' => $request->cnic_number,
            'cnic_image'  => $cnicPath,
            'shop_name'   => $request->shop_name,
            'owner_name'  => $request->owner_name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'description' => $request->description,
            'is_approved' => false,
        ]);

        // ── Save rice categories ──────────────────────────────────────────
        $categories = json_decode($request->rice_categories, true);

        if (is_array($categories)) {
            foreach ($categories as $cat) {
                RiceCategory::create([
                    'shop_id'      => $shop->id,
                    'name'         => $cat['name'] ?? '',
                    'price_per_kg' => $cat['price_per_kg'] ?? 0,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully. Awaiting approval.',
            'data'    => $shop->load('riceCategories'),
        ], 201);
    }

    // ── Get authenticated user's shop ─────────────────────────────────────
    public function myShop()
    {
        $shop = Shop::where('user_id', Auth::id())
            ->with('riceCategories')
            ->first();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'No shop found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $shop,
        ]);
    }
     // ── Update Shop ───────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $shop = Shop::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'shop_name'       => 'required|string|max:255',
            'owner_name'      => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string|max:500',
            'description'     => 'nullable|string|max:1000',
            'rice_categories' => 'nullable|string',
            'cnic_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Update CNIC image only if a new one was uploaded
        if ($request->hasFile('cnic_image')) {
            Storage::disk('public')->delete($shop->cnic_image);
            $shop->cnic_image = $request->file('cnic_image')
                                        ->store('cnic_images', 'public');
        }

        $shop->update([
            'shop_name'   => $request->shop_name,
            'owner_name'  => $request->owner_name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'description' => $request->description,
        ]);

        // Sync rice categories
        if ($request->filled('rice_categories')) {
            $categories = json_decode($request->rice_categories, true);
            if (is_array($categories)) {
                // Delete old ones and re-insert (simplest approach)
                $shop->riceCategories()->delete();
                foreach ($categories as $cat) {
                    RiceCategory::create([
                        'shop_id'      => $shop->id,
                        'name'         => $cat['name'] ?? '',
                        'price_per_kg' => $cat['price_per_kg'] ?? 0,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully.',
            'data'    => $shop->load('riceCategories'),
        ]);
    }

    // ── Delete Shop ───────────────────────────────────────────────────────
    public function destroy($id)
    {
        $shop = Shop::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        // Delete CNIC image from storage
        Storage::disk('public')->delete($shop->cnic_image);

        $shop->delete(); // cascades to rice_categories

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully.',
        ]);
    }
}
