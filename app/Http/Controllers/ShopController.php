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
    // ── Helper: build full public URL ─────────────────────────────────────
    private function publicUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    // ── Helper: transform shop for response (resolve image URLs) ─────────
    private function transformShop(Shop $shop): Shop
    {
        $rawCnic = $shop->getRawOriginal('cnic_image');
        if ($rawCnic) {
            $shop->cnic_image = $this->publicUrl($rawCnic);
        }

        $shop->riceCategories->each(function ($cat) {
            $rawImg = $cat->getRawOriginal('image');
            if ($rawImg) {
                $cat->image = $this->publicUrl($rawImg);
            }
        });

        return $shop;
    }

    // ── Helper: delete + recreate rice categories ─────────────────────────
    // Category images arrive as: rice_image_0, rice_image_1, ...
    // Existing images (not re-uploaded) keep their old path via 'existing_image_N'
    private function saveCategories(Shop $shop, string $categoriesJson, Request $request): void
    {
        $categories = json_decode($categoriesJson, true);
        if (!is_array($categories)) return;

        // Collect old image paths before deleting records
        $oldImages = $shop->riceCategories->pluck('image')->filter()->toArray();

        // Determine which old paths are being reused
        $reusedPaths = [];
        foreach ($categories as $index => $cat) {
            $existingKey = 'existing_image_' . $index;
            if (!empty($cat[$existingKey])) {
                $reusedPaths[] = $cat[$existingKey]; // raw storage path, not URL
            }
        }

        // Delete old images that are NOT being reused
        foreach ($oldImages as $oldPath) {
            if (!in_array($oldPath, $reusedPaths) && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Delete old records
        $shop->riceCategories()->delete();

        foreach ($categories as $index => $cat) {
            $imagePath = null;

            $fileKey = 'rice_image_' . $index;
            if ($request->hasFile($fileKey)) {
                // New image uploaded for this category
                $imagePath = $request->file($fileKey)->store('rice_images', 'public');
            } elseif (!empty($cat['existing_image'])) {
                // Keep the old storage path (sent back from Flutter)
                $imagePath = $cat['existing_image'];
            }

            RiceCategory::create([
                'shop_id'      => $shop->id,
                'name'         => trim($cat['name'] ?? ''),
                'price_per_kg' => (float)($cat['price_per_kg'] ?? 0),
                'stock_kg'     => (float)($cat['stock_kg'] ?? 0),
                'image'        => $imagePath,
            ]);
        }
    }

    // ── CREATE SHOP ───────────────────────────────────────────────────────
    // POST /api/shops
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cnic_number'     => 'required',
            'cnic_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
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

            $existing = Shop::where('user_id', $user->id)->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a shop. Delete it first to create a new one.',
                ], 409);
            }

            $cnicImagePath = null;
            if ($request->hasFile('cnic_image')) {
                $cnicImagePath = $request->file('cnic_image')->store('cnic_images', 'public');
            }

            $shop = Shop::create([
                'user_id'     => $user->id,
                'cnic_number' => $request->cnic_number,
                'cnic_image'  => $cnicImagePath,
                'shop_name'   => $request->shop_name,
                'owner_name'  => $request->owner_name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'description' => $request->description ?? '',
                'is_approved' => false,
            ]);

            $this->saveCategories($shop, $request->rice_categories, $request);

            $shop->load('riceCategories');
            $this->transformShop($shop);

            return response()->json([
                'success' => true,
                'message' => 'Shop created successfully. Awaiting admin approval.',
                'data'    => $shop,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating shop: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── GET MY SHOP ───────────────────────────────────────────────────────
    // GET /api/shops/my-shop
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

            $this->transformShop($shop);

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

    // ── UPDATE SHOP ───────────────────────────────────────────────────────
    // POST /api/shops/{id}  (Flutter sends _method=PUT)
    public function update(Request $request, $id)
    {
        try {
            $shop = Shop::where('id', $id)
                ->where('user_id', Auth::id())
                ->with('riceCategories')
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'cnic_number'     => 'nullable|string|regex:/^\d{5}-\d{7}-\d{1}$/',
                'cnic_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
                'shop_name'       => 'required|string|max:255',
                'owner_name'      => 'required|string|max:255',
                'phone'           => 'required|string|max:20',
                'address'         => 'required|string|max:500',
                'description'     => 'nullable|string|max:1000',
                'rice_categories' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Update CNIC image if new one provided
            if ($request->hasFile('cnic_image')) {
                $rawOld = $shop->getRawOriginal('cnic_image');
                if ($rawOld && Storage::disk('public')->exists($rawOld)) {
                    Storage::disk('public')->delete($rawOld);
                }
                $shop->cnic_image = $request->file('cnic_image')->store('cnic_images', 'public');
                $shop->save();
            }

            $shop->update([
                'cnic_number' => $request->cnic_number ?? $shop->cnic_number,
                'shop_name'   => $request->shop_name,
                'owner_name'  => $request->owner_name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'description' => $request->description ?? '',
            ]);

            if ($request->filled('rice_categories')) {
                $shop->load('riceCategories'); // refresh before diff
                $this->saveCategories($shop, $request->rice_categories, $request);
            }

            $shop->refresh()->load('riceCategories');
            $this->transformShop($shop);

            return response()->json([
                'success' => true,
                'message' => 'Shop updated successfully.',
                'data'    => $shop,
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

    // ── DELETE SHOP ───────────────────────────────────────────────────────
    public function destroy($id)
    {
        try {
            $shop = Shop::where('id', $id)
                ->where('user_id', Auth::id())
                ->with('riceCategories')
                ->firstOrFail();

            if ($shop->cnic_image) {
                $raw = $shop->getRawOriginal('cnic_image');
                if ($raw && Storage::disk('public')->exists($raw)) {
                    Storage::disk('public')->delete($raw);
                }
            }

            $shop->riceCategories->each(function ($cat) {
                $raw = $cat->getRawOriginal('image');
                if ($raw && Storage::disk('public')->exists($raw)) {
                    Storage::disk('public')->delete($raw);
                }
            });

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

    // ── ALL APPROVED SHOPS ────────────────────────────────────────────────
    public function index()
    {
        try {
            $shops = Shop::where('is_approved', true)
                ->with('riceCategories', 'user:id,name')
                ->paginate(20);

            $shops->getCollection()->transform(fn($s) => $this->transformShop($s));

            return response()->json(['success' => true, 'data' => $shops], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── SEARCH SHOPS ──────────────────────────────────────────────────────
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

            $shops->getCollection()->transform(fn($s) => $this->transformShop($s));

            return response()->json(['success' => true, 'data' => $shops], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
