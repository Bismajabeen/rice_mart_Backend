<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\RiceCategory;
use App\Http\Controllers\NotificationController;


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

        // One shop per user
        $existingShop = Shop::where('user_id', $request->user()->id)->exists();

        if ($existingShop) {
            return response()->json([
                'success' => false,
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
            'message' => 'Shop submitted for approval',
            'shop' => $shop
        ]);
    }

    // =========================
    // PENDING SHOPS (ADMIN)
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
      // APPROVE SHOP (ADMIN ONLY)
      // CUSTOMER → SELLER
      // =========================
      public function approve($id)
    {

     $shop = Shop::findOrFail($id);

      $shop->update([
        'is_approved' => 1,
        'status' => 'approved'
      ]);

      // GET SHOP OWNER
     $user = $shop->user;

     // ASSIGN SELLER ROLE
     $user->removeRole('customer');
     $user->assignRole('seller');

     // CREATE NOTIFICATION
     NotificationController::createNotification(

        $shop->user_id,

        'Shop Approved',

        'Congratulations! Your shop has been approved.',

        'shop_approved'
     );

     return response()->json([
        'success' => true,
        'shop' => $shop
     ]);
    }

    // admin cretae shop 
    public function adminCreateSeller(Request $request){
       $request->validate([

        // USER
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',

        // SHOP
        'shop_name' => 'required',
        'owner_name' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'cnic' => 'required',

        // IMAGE
        'cnic_image' => 'nullable',  // (change it to require )
       ]);

        // =========================
        // CREATE USER
       // =========================

       $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
         ]);

        // =========================
       // ASSIGN SELLER ROLE
       // =========================

      $user->assignRole('seller');

       // =========================
       // IMAGE UPLOAD
      // =========================

      $magePath = null;

    //  if ($request->hasFile('cnic_image')) {

    //      $imagePath = $request
    //         ->file('cnic_image')
    //         ->store('cnic_images', 'public');
    //  }

      // =========================
       // CREATE SHOP
      // =========================

     $shop = Shop::create([

        'user_id' => $user->id,

        'shop_name' => $request->shop_name,
        'owner_name' => $request->owner_name,
        'phone' => $request->phone,
        'address' => $request->address,

        'cnic' => $request->cnic,
        'cnic_image' => null,

        'description' => $request->description,

        // AUTO APPROVED
        'is_approved' => 1,
        'status' => 'approved',
     ]);

      return response()->json([
        'success' => true,
        'message' => 'Seller created successfully',
        'user' => $user,
        'shop' => $shop,
     ]);
    }
    // =========================
    // REJECT SHOP (ADMIN ONLY)
    // =========================
    public function reject($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop rejected and deleted'
        ]);
    }

    // =========================
    // UPDATE SHOP (RE-APPROVAL REQUIRED)
    // =========================
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

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

            // BACK TO PENDING
            'status' => 'pending',
            'is_approved' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shop updated and sent for re-approval',
            'shop' => $shop
        ]);
    }

    // =========================
    // DELETE SHOP (SELLER → CUSTOMER)
    // =========================
    public function deleteShop(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        if ($shop->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = $shop->user;

        $shop->delete();

        // ROLE REVERT
        if ($user->hasRole('seller')) {
            $user->removeRole('seller');
        }

        $user->assignRole('customer');

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted and user reverted to customer'
        ]);
    }

    // =========================
    // UPDATE RICE (KEEP YOUR LOGIC)
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