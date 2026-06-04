<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\RiceCategory;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    // =========================
    // CREATE SHOP (CUSTOMER)
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'cnic' => 'required',
            'shop_name' => 'required',
            'owner_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        if (Shop::where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'You already have a shop'
            ], 400);
        }

        $shop = Shop::create([
            'user_id' => $request->user()->id,
            'cnic' => $request->cnic,
            'cnic_image' => $request->cnic_image ?? null,
            'shop_name' => $request->shop_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description ?? null,
            'status' => 'pending',
            'is_approved' => 0,
        ]);

        return response()->json([
            'success' => true,
            'shop' => $shop
        ]);
    }

    // =========================
    // PENDING SHOPS
    // =========================
    public function pendingShops()
    {
        return response()->json(
            Shop::latest()->where('is_approved', 0)->get()
        );
    }

    // =========================
    // APPROVED SHOPS
    // =========================
    public function approvedShops()
    {
        return response()->json(
            Shop::latest()->where('is_approved', 1)->get()
        );
    }

    // =========================
    // APPROVE SHOP
    // =========================
    public function approve($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->update([
            'is_approved' => 1,
            'status' => 'approved'
        ]);

        $user = $shop->user;

        if ($user) {
            $user->syncRoles(['seller']);
        }

        return response()->json([
            'success' => true,
            'shop' => $shop
        ]);
    }

    // =========================
    // ADMIN CREATE SELLER (SAFE TRANSACTION)
    // =========================
    public function adminCreateSeller(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'shop_name' => 'required',
            'owner_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'cnic' => 'required',
        ]);

        return DB::transaction(function () use ($request) {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $user->syncRoles(['seller']);

            $shop = Shop::create([
                'user_id' => $user->id,
                'shop_name' => $request->shop_name,
                'owner_name' => $request->owner_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'cnic_image' => null,
                'description' => $request->description,
                'is_approved' => 1,
                'status' => 'approved',
            ]);

            return response()->json([
                'success' => true,
                'user' => $user,
                'shop' => $shop,
            ]);
        });
    }

    // =========================
    // UPDATE SHOP
    // =========================
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        if (
            $shop->user_id != $request->user()->id &&
            !$request->user()->hasRole('admin') &&
            !$request->user()->hasRole('super_admin')
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'shop_name' => 'required',
            'owner_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $shop->update([
            'shop_name' => $request->shop_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'shop' => $shop
        ]);
    }

    // =========================
    // DELETE SHOP
    // =========================
    public function deleteShop(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        if (
            $shop->user_id != $request->user()->id &&
            !$request->user()->hasRole('admin') &&
            !$request->user()->hasRole('super_admin')
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = $shop->user;

        $shop->delete();

        if ($user) {
            $user->syncRoles(['customer']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted'
        ]);
    }

    // =========================
    // UPDATE RICE (FIXED)
    // =========================
    public function updateRice(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $rice = RiceCategory::findOrFail($id);

        $rice->update([
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json([
            'success' => true,
            'rice' => $rice
        ]);
    }
}