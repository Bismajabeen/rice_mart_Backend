<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\RiceCategory;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class ProductController extends Controller
{
    // =========================
    // LOW STOCK THRESHOLD
    // (isse badal ke apni marzi ka number rakh sakti ho)
    // =========================
    const LOW_STOCK_THRESHOLD = 5;

    // =========================
    // CREATE PRODUCT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'rice_category_id' => 'required|exists:rice_categories,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // ✅ Ownership check (shop must belong to seller)
        $shop = Shop::where('id', $request->shop_id)
            ->where('user_id', auth()->id())
            ->where('is_approved', 1)
            ->where('status', 'approved')
            ->first();

        if (!$shop) {
            return response()->json([
                'message' => 'You are not allowed to use this shop'
            ], 403);
        }

        // ✅ Category check
        $category = RiceCategory::where('id', $request->rice_category_id)
            ->where('status', true)
            ->first();

        if (!$category) {
            return response()->json([
                'message' => 'Invalid or inactive category'
            ], 400);
        }

        // ✅ Handle image upload (stored on disk, path saved in DB)

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'user_id' => auth()->id(),
            'shop_id' => $request->shop_id,
            'rice_category_id' => $request->rice_category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        // =========================
        // NOTIFY SELLER — new product already low on stock
        // =========================
        if ($product->stock <= self::LOW_STOCK_THRESHOLD) {
            NotificationService::send(
                $shop->user,
                'low_stock',
                'Low stock',
                '"' . $product->name . '" was added with only ' . $product->stock . ' left in stock.',
                ['product_id' => $product->id, 'shop_id' => $shop->id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    // =========================
    // SHOP PRODUCTS (PUBLIC)
    // =========================
    public function shopProducts($shopId)
    {
        $products = Product::with(['shop', 'riceCategory'])
            ->where('shop_id', $shopId)
            ->where('is_active', true)
            ->whereHas('shop', function ($query) {
                $query->where('is_approved', 1)
                    ->where('status', 'approved');
            })
            ->latest()
            ->get();

        return response()->json($products);
    }

    // =========================
    // ALL PRODUCTS (PUBLIC)
    // =========================
    public function allProducts()
    {
        return Product::with(['shop', 'riceCategory'])
        ->where('is_active', true)
        ->whereHas('shop', function ($query) {
            $query->where('is_approved', 1)
                ->where('status', 'approved');
            })
            ->latest()
            ->get();
    }

    // =========================
    // UPDATE PRODUCT
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // ✅ Ownership check through shop
        $product = Product::with('shop.user')
            ->where('id', $id)
            ->whereHas('shop', function ($query) {
                $query->where('user_id', auth()->id());
                $query->where('is_approved', 1);
                $query->where('status', 'approved');
            })
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Unauthorized access to product'
            ], 403);
        }

        // Track whether we're crossing INTO low stock (so we don't spam a
        // notification every single time it's edited while already low)
        $wasAboveThreshold = $product->stock > self::LOW_STOCK_THRESHOLD;

        $updateData = [
            'price' => $request->price,
            'stock' => $request->stock,
        ];

        // ✅ Replace image only if a new one is uploaded
        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $updateData['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($updateData);

        // =========================
        // NOTIFY SELLER — stock just dropped to/below threshold
        // =========================
        if ($wasAboveThreshold && $product->stock <= self::LOW_STOCK_THRESHOLD) {
            NotificationService::send(
                $product->shop->user,
                'low_stock',
                'Low stock',
                '"' . $product->name . '" is running low — only ' . $product->stock . ' left.',
                ['product_id' => $product->id, 'shop_id' => $product->shop_id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    // =========================
    // DELETE PRODUCT
    // =========================
    public function delete($id)
    {
        // ✅ Ownership check through shop
        $product = Product::where('id', $id)
         ->whereHas('shop', function ($query) {
            $query->where('user_id', auth()->id());
            })
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Unauthorized access to product'
            ], 403);
        }

        // ✅ Remove image file from disk too
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
