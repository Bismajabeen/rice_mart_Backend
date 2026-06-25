<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('seller:id,name')
            ->where('status', 'approved')
            ->latest()
            ->get();
        return response()->json(['products' => $products], 200);
    }

    public function show($id)
    {
        $product = Product::with('seller:id,name')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
        return response()->json(['product' => $product], 200);
    }

    public function store(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $shop = Shop::where('seller_id', $user->id)->first();
        if (!$shop) {
            return response()->json(['message' => 'Pehle apni shop banao.'], 400);
        }

        $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'seller_id'   => $user->id,
            'shop_id'     => $shop->id,
            'name'        => $request->name,
            'description' => $request->description ?? '',
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category'    => $request->category ?? 'rice',
            'image'       => $imagePath,
            'status'      => $shop->status === 'approved' ? 'approved' : 'pending',
        ]);

        return response()->json([
            'message' => 'Product add ho gaya!',
            'product' => $product,
        ], 201);
    }

    public function myProducts(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $products = Product::where('seller_id', $user->id)->latest()->get();
        return response()->json(['products' => $products], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $product = Product::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update($request->only([
            'name', 'description', 'price', 'stock', 'category'
        ]));

        return response()->json(['message' => 'Product updated!', 'product' => $product], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $product = Product::where('id', $id)->where('seller_id', $user->id)->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted!'], 200);
    }

    public function allProducts(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $products = Product::with('seller:id,name')->latest()->get();
        return response()->json(['products' => $products], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->status = $request->status;
        $product->save();

        return response()->json(['message' => 'Status updated.', 'product' => $product], 200);
    }
}