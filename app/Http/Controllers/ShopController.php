<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    // ── Helper: resolve full public URL ──────────────────────────────────
    private function publicUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    // ── Helper: build response shape for shop ─────────────────────────────
    // Resolves cnic_image and every category image to full URLs
    private function transformShop(Shop $shop): array
    {
        $data = $shop->toArray();

        // Resolve CNIC image URL
        if (!empty($data['cnic_image'])) {
            $data['cnic_image'] = $this->publicUrl($shop->getRawOriginal('cnic_image'));
        }

        // Resolve each category image URL
        $categories = $data['rice_categories'] ?? [];
        foreach ($categories as &$cat) {
            if (!empty($cat['image'])) {
                $cat['image_url'] = $this->publicUrl($cat['image']); // full URL for display
                // 'image' stays as raw path so Flutter can send it back on update
            }
        }
        unset($cat);
        $data['rice_categories'] = $categories;

        return $data;
    }

    // ── Helper: upload category images and build categories array ─────────
    // rice_image_0, rice_image_1 ... come as multipart files
    // rice_categories JSON: [{ name, price_per_kg, stock_kg, image? }]
    // 'image' in JSON = existing raw path to keep (on update)
    private function buildCategories(Request $request, array $old = []): array
    {
        $raw = json_decode($request->input('rice_categories', '[]'), true);
        if (!is_array($raw)) $raw = [];

        // Collect old paths so we can delete ones not reused
        $oldPaths  = array_column($old, 'image');
        $reusedPaths = [];

        $categories = [];

        foreach ($raw as $i => $cat) {
            $imagePath = null;
            $fileKey   = 'rice_image_' . $i;

            if ($request->hasFile($fileKey)) {
                // New image uploaded
                $imagePath = $request->file($fileKey)->store('rice_images', 'public');
            } elseif (!empty($cat['image'])) {
                // Keep existing raw path
                $imagePath = $cat['image'];
                $reusedPaths[] = $imagePath;
            }

            $categories[] = [
                'name'         => trim($cat['name']         ?? ''),
                'price_per_kg' => (float)($cat['price_per_kg'] ?? 0),
                'stock_kg'     => (float)($cat['stock_kg']     ?? 0),
                'image'        => $imagePath, // raw storage path
            ];
        }

        // Delete old images that are no longer used
        foreach ($oldPaths as $oldPath) {
            if ($oldPath && !in_array($oldPath, $reusedPaths)) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        return $categories;
    }

    // ── CREATE SHOP ───────────────────────────────────────────────────────
    // POST /api/shops
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cnic_number'     => ['required', 'string', 'regex:/^\d{5}-\d{7}-\d$/', 'unique:shops,cnic_number'],
            'cnic_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
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

            if (Shop::where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a shop. Delete it first.',
                ], 409);
            }

            // Upload CNIC image
            $cnicPath = null;
            if ($request->hasFile('cnic_image')) {
                $cnicPath = $request->file('cnic_image')->store('cnic_images', 'public');
            }

            // Build categories JSON with uploaded images
            $categories = $this->buildCategories($request);

            $shop = Shop::create([
                'user_id'         => $user->id,
                'cnic_number'     => $request->cnic_number,
                'cnic_image'      => $cnicPath,
                'shop_name'       => $request->shop_name,
                'owner_name'      => $request->owner_name,
                'phone'           => $request->phone,
                'address'         => $request->address,
                'description'     => $request->description ?? '',
                'is_approved'     => false,
                'rice_categories' => $categories,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shop created. Awaiting admin approval.',
                'data'    => $this->transformShop($shop),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── GET MY SHOP ───────────────────────────────────────────────────────
    // GET /api/shops/my-shop
    public function myShop()
    {
        try {
            $shop = Shop::where('user_id', Auth::id())->first();

            if (!$shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have a shop yet.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $this->transformShop($shop),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ── UPDATE SHOP ───────────────────────────────────────────────────────
    // POST /api/shops/{id}  (_method=PUT)
    public function update(Request $request, $id)
    {
        try {
            $shop = Shop::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'cnic_number'     => ['nullable', 'string', 'regex:/^\d{5}-\d{7}-\d$/'],
                'cnic_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
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
                $oldRaw = $shop->getRawOriginal('cnic_image');
                if ($oldRaw && Storage::disk('public')->exists($oldRaw)) {
                    Storage::disk('public')->delete($oldRaw);
                }
                $shop->cnic_image = $request->file('cnic_image')
                    ->store('cnic_images', 'public');
                $shop->save();
            }

            // Build updated categories (handles image uploads + deletions)
            $oldCategories = $shop->rice_categories ?? [];
            $newCategories = $request->filled('rice_categories')
                ? $this->buildCategories($request, $oldCategories)
                : $oldCategories;

            $shop->update([
                'cnic_number'     => $request->cnic_number ?? $shop->cnic_number,
                'shop_name'       => $request->shop_name,
                'owner_name'      => $request->owner_name,
                'phone'           => $request->phone,
                'address'         => $request->address,
                'description'     => $request->description ?? '',
                'rice_categories' => $newCategories,
            ]);

            $shop->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Shop updated successfully.',
                'data'    => $this->transformShop($shop),
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ── DELETE SHOP ───────────────────────────────────────────────────────
    public function destroy($id)
    {
        try {
            $shop = Shop::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Delete CNIC image
            $rawCnic = $shop->getRawOriginal('cnic_image');
            if ($rawCnic && Storage::disk('public')->exists($rawCnic)) {
                Storage::disk('public')->delete($rawCnic);
            }

            // Delete all category images
            foreach ($shop->rice_categories ?? [] as $cat) {
                if (!empty($cat['image']) && Storage::disk('public')->exists($cat['image'])) {
                    Storage::disk('public')->delete($cat['image']);
                }
            }

            $shop->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shop deleted successfully.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Shop not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── ALL APPROVED SHOPS ────────────────────────────────────────────────
    // GET /api/shops
    public function index()
    {
        try {
            $shops = Shop::where('is_approved', true)
                ->with('user:id,name')
                ->paginate(20);

            $shops->getCollection()->transform(
                fn($s) => $this->transformShop($s)
            );

            return response()->json(['success' => true, 'data' => $shops], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── SEARCH SHOPS ──────────────────────────────────────────────────────
    // GET /api/shops/search?q=
    public function search(Request $request)
    {
        try {
            $q = $request->input('q', '');

            $shops = Shop::where('is_approved', true)
                ->where(fn($qb) => $qb
                    ->where('shop_name',  'like', "%$q%")
                    ->orWhere('owner_name', 'like', "%$q%")
                    ->orWhere('address',    'like', "%$q%")
                )
                ->with('user:id,name')
                ->paginate(20);

            $shops->getCollection()->transform(
                fn($s) => $this->transformShop($s)
            );

            return response()->json(['success' => true, 'data' => $shops], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
