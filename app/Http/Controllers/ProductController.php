<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\RiceCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // =========================
    // ADD PRODUCT
    // =========================
    public function store(Request $request)
    {
        $request->validate([

            'shop_id' => 'required|exists:shops,id',

            'rice_category_id' => 'required|exists:rice_categories,id',

            'name' => 'required',

            'price' => 'required|numeric',

            'stock' => 'required|numeric',
        ]);

        // =========================
        // CHECK SHOP OWNERSHIP
        // =========================

        $shop = Shop::where('id', $request->shop_id)
            ->where('user_id', auth()->id())
            ->where('is_approved', 1)
            ->first();

        if (!$shop) {

            return response()->json([
                'message' => 'Unauthorized shop access'
            ], 403);
        }

        // =========================
        // CHECK CATEGORY ACTIVE
        // =========================

        $category = RiceCategory::where('id', $request->rice_category_id)
            ->where('status', true)
            ->first();

        if (!$category) {

            return response()->json([
                'message' => 'Rice category inactive'
            ], 400);
        }

        // =========================
        // CREATE PRODUCT
        // =========================

        $product = Product::create([

            'user_id' => auth()->id(),

            'shop_id' => $request->shop_id,

            'rice_category_id' => $request->rice_category_id,

            'name' => $request->name,

            'price' => $request->price,

            'stock' => $request->stock,

            'image' => null,
        ]);

        return response()->json([

            'success' => true,

            'message' => 'Product added successfully',

            'product' => $product,
        ]);
    }

    // =========================
    // FETCH SHOP PRODUCTS
    // =========================
    public function shopProducts($shopId)
    {
        $products = Product::with([

            'shop',
            'riceCategory',

        ])
            ->where('shop_id', $shopId)

            ->latest()

            ->get();

        return response()->json($products);
    }

    // =========================
    // FETCH ALL PRODUCTS
    // =========================
    public function allProducts()
    {
        $products = Product::with([

            'shop',
            'riceCategory',

        ])
            ->latest()

            ->get();

        return response()->json($products);
    }

    // =========================
    // UPDATE PRODUCT
    // =========================
    public function update(Request $request, $id)
    {
        // =========================
        // CHECK PRODUCT OWNERSHIP
        // =========================

        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$product) {

            return response()->json([
                'message' => 'Product not found or unauthorized'
            ], 404);
        }

        $request->validate([

            'price' => 'required|numeric',

            'stock' => 'required|numeric',
        ]);

        $product->update([

            'price' => $request->price,

            'stock' => $request->stock,
        ]);

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
        // =========================
        // CHECK PRODUCT OWNERSHIP
        // =========================

        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$product) {

            return response()->json([
                'message' => 'Product not found or unauthorized'
            ], 404);
        }

        $product->delete();

        return response()->json([

            'success' => true,

            'message' => 'Product deleted successfully'
        ]);
    }
}