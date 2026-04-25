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
            'cnic_number'     => 'required|string|regex:/^\d{5}-\d{7}-\d{1}$/|unique:shops,cnic_number',
            'cnic_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // ✅ FIXED: was missing
            'shop_name'       => 'required|string|max:255',
            'owner_name'      => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string|max:500',
            'description'     => 'nullable|string|max:1000',
            'rice_categories' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            // ── Check if user already has a shop ──────────────────────────
            $existingShop = Shop::where('user_id', $user->id)->first();
            if ($existingShop) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a shop. Delete it first to create a new one.',
                ], 409);
            }

            // ── Handle CNIC image upload ──────────────────────────────────
            $cnicImagePath = null;
            if ($request->hasFile('cnic_image')) {
                $cnicImagePath = $request->file('cnic_image')
                    ->store('cnic_images', 'public');
            }

            // ── Create shop ───────────────────────────────────────────────
            $shop = Shop::create([
                'user_id'     => $user->id,
                'cnic_number' => $request->cnic_number,
                'cnic_image'  => $cnicImagePath, // ✅ FIXED: was missing
                'shop_name'   => $request->shop_name,
                'owner_name'  => $request->owner_name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'description' => $request->description ?? '',
                'is_approved' => false,
            ]);

            // ── Save rice categories ──────────────────────────────────────
            $categories = json_decode($request->rice_categories, true);

            if (is_array($categories) && count($categories) > 0) {
                foreach ($categories as $cat) {
                    RiceCategory::create([
                        'shop_id'      => $shop->id,
                        'name'         => trim($cat['name'] ?? ''),
                        'price_per_kg' => (float)($cat['price_per_kg'] ?? 0),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Shop created successfully. Awaiting admin approval.',
                'data'    => $shop->load('riceCategories'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating shop: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Get authenticated user's shop ─────────────────────────────────────
    public function myShop()
    {
        try {
            $shop = Shop::where('user_id', Auth::id())
                ->with('riceCategories')
                ->first();

            if (!$shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have a shop yet.',
                ], 404);
            }

            // ✅ FIXED: Return full public URL for cnic_image
            if ($shop->cnic_image) {
                $shop->cnic_image = Storage::disk('public')->url($shop->cnic_image);
            }

            return response()->json([
                'success' => true,
                'data'    => $shop,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching shop: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Update Shop ───────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        try {
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
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // ── Update CNIC image if provided ─────────────────────────────
            if ($request->hasFile('cnic_image')) {
                if ($shop->cnic_image && Storage::disk('public')->exists($shop->cnic_image)) {
                    Storage::disk('public')->delete($shop->cnic_image);
                }
                $shop->cnic_image = $request->file('cnic_image')
                    ->store('cnic_images', 'public');
                $shop->save();
            }

            // ── Update shop details ───────────────────────────────────────
            $shop->update([
                'shop_name'   => $request->shop_name,
                'owner_name'  => $request->owner_name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'description' => $request->description ?? '',
            ]);

            // ── Update rice categories ────────────────────────────────────
            if ($request->filled('rice_categories')) {
                $categories = json_decode($request->rice_categories, true);

                if (is_array($categories)) {
                    $shop->riceCategories()->delete();

                    foreach ($categories as $cat) {
                        RiceCategory::create([
                            'shop_id'      => $shop->id,
                            'name'         => trim($cat['name'] ?? ''),
                            'price_per_kg' => (float)($cat['price_per_kg'] ?? 0),
                        ]);
                    }
                }
            }

            // ✅ FIXED: Return full public URL for cnic_image
            $shop->refresh();
            if ($shop->cnic_image) {
                $shop->cnic_image = Storage::disk('public')->url($shop->cnic_image);
            }

            return response()->json([
                'success' => true,
                'message' => 'Shop updated successfully.',
                'data'    => $shop->load('riceCategories'),
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found or unauthorized.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating shop: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Delete Shop ───────────────────────────────────────────────────────
    public function destroy($id)
    {
        try {
            $shop = Shop::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($shop->cnic_image && Storage::disk('public')->exists($shop->cnic_image)) {
                Storage::disk('public')->delete($shop->cnic_image);
            }

            $shop->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shop deleted successfully.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found or unauthorized.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting shop: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Get all approved shops (public) ──────────────────────────────────
    public function index()
    {
        try {
            $shops = Shop::where('is_approved', true)
                ->with('riceCategories', 'user:id,name')
                ->paginate(20);

            // ✅ FIXED: Append full URL for each shop's cnic_image
            $shops->getCollection()->transform(function ($shop) {
                if ($shop->cnic_image) {
                    $shop->cnic_image = Storage::disk('public')->url($shop->cnic_image);
                }
                return $shop;
            });

            return response()->json([
                'success' => true,
                'data'    => $shops,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching shops: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Search shops ──────────────────────────────────────────────────────
    public function search(Request $request)
    {
        try {
            $query = $request->input('q');

            $shops = Shop::where('is_approved', true)
                ->where(function ($q) use ($query) {
                    $q->where('shop_name', 'like', "%$query%")
                      ->orWhere('owner_name', 'like', "%$query%")
                      ->orWhere('address', 'like', "%$query%");
                })
                ->with('riceCategories', 'user:id,name')
                ->paginate(20);

            // ✅ FIXED: Append full URL for each shop's cnic_image
            $shops->getCollection()->transform(function ($shop) {
                if ($shop->cnic_image) {
                    $shop->cnic_image = Storage::disk('public')->url($shop->cnic_image);
                }
                return $shop;
            });

            return response()->json([
                'success' => true,
                'data'    => $shops,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching shops: ' . $e->getMessage(),
            ], 500);
        }
    }
}
