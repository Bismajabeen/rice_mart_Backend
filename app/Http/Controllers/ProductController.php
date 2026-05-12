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

        // CHECK SHOP
        $shop = Shop::find($request->shop_id);

        if (!$shop) {

            return response()->json([
                'message' => 'Shop not found'
            ], 404);
        }

        // CHECK CATEGORY ACTIVE
        $category = RiceCategory::where('id', $request->rice_category_id)
            ->where('status', true)
            ->first();

        if (!$category) {

            return response()->json([
                'message' => 'Rice category inactive'
            ], 400);
        }

        // CREATE PRODUCT
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

            'message' => 'Product added successfully',

            'product' => $product,
        ]);
    }

    // =========================
    // FETCH SHOP PRODUCTS
    // =========================
    public function shopProducts($shopId)
    {
        $products = Product::with('riceCategory')

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
    // DELETE PRODUCT
    // =========================
    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {

            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    // =========================
    // UPDATE PRODUCT
    // =========================
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {

            return response()->json([
                'message' => 'Product not found'
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

            'message' => 'Product updated successfully',

            'product' => $product,
        ]);
    }
}