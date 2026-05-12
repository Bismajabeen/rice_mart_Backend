<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\RiceCategory;

class ShopController extends Controller
{

    // =========================
    // CREATE SHOP
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

        // CHECK IF USER ALREADY HAS SHOP
        $existingShop = Shop::where(
            'user_id',
            $request->user()->id
        )->exists();

        if ($existingShop) {

            return response()->json([
                'success' => false,
                'message' => 'You already have a shop'
            ], 400);
        }

        // CREATE SHOP
        $shop = Shop::create([

            'user_id' => $request->user()->id,

            'cnic' => $request->cnic,
            'cnic_image' => $request->cnic_image,

            'shop_name' => $request->shop_name,
            'owner_name' => $request->owner_name,

            'phone' => $request->phone,
            'address' => $request->address,

            'description' => $request->description,

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

        Shop::where('is_approved', 0)
            ->latest()
            ->get()

    );
}



    // =========================
    // APPROVED SHOPS
    // =========================
    public function approvedShops()
{
    return response()->json(

        Shop::where('is_approved', 1)
            ->latest()
            ->get()

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

        return response()->json([
            'success' => true,
            'shop' => $shop
        ]);
    }



    // =========================
    // REJECT / DELETE SHOP
    // =========================
    public function reject($id)
    {

        $shop = Shop::findOrFail($id);

        $shop->delete();

        return response()->json([
            'success' => true
        ]);
    }

        // =========================
    // UPDATE SHOP
    // =========================
    public function update(Request $request, $id)
    {

        $shop = Shop::findOrFail($id);

        // SECURITY
        if ($shop->user_id != $request->user()->id) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
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

            // RE-APPROVAL REQUIRED
            'status' => 'pending',
            'is_approved' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shop updated and sent for approval',
            'shop' => $shop
        ]);
    }



    // =========================
    // SELLER DELETE SHOP
    // =========================
    public function deleteShop(Request $request, $id)
    {

        $shop = Shop::findOrFail($id);

        // SECURITY
        if ($shop->user_id != $request->user()->id) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully'
        ]);
    }

    // =========================
    // UPDATE RICE
    // =========================
    public function updateRice(Request $request, $id)
    {

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