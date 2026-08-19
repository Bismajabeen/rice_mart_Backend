<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\RiceCategory;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class ShopController extends Controller
{
    // =========================
    // CREATE SHOP (CUSTOMER)
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'cnic' => 'required|string|max:20',
            'shop_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'description' => 'nullable|string',

            // CNIC has two sides — both are required on first submission
            'cnic_image' => 'required|image|max:2048',
            'cnic_back_image' => 'required|image|max:2048',
        ]);

        if (Shop::where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'You already have a shop'
            ], 400);
        }

        $cnicImagePath = $request->file('cnic_image')->store('shops/cnic', 'public');
        $cnicBackImagePath = $request->file('cnic_back_image')->store('shops/cnic', 'public');

        $shop = Shop::create([
            'user_id' => $request->user()->id,
            'cnic' => $request->cnic,
            'cnic_image' => $cnicImagePath,
            'cnic_back_image' => $cnicBackImagePath,
            'shop_name' => $request->shop_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'city' => $request->city,
            'address' => $request->address,
            'description' => $request->description ?? null,
            'status' => 'pending',
            'is_approved' => 0,
        ]);

        // =========================
        // NOTIFY ADMINS — new shop needs review
        // =========================
        NotificationService::sendToAdmins(
            'shop_pending',
            'New shop pending approval',
            $shop->shop_name . ' has submitted a shop for approval.',
            ['shop_id' => $shop->id]
        );

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
            'status' => 'approved',
            // clear any outstanding correction note — nothing left to fix
            'correction_reason' => null,
            'correction_requested_at' => null,
        ]);

        $user = $shop->user;

        if ($user) {
            $user->syncRoles(['seller']);

            // =========================
            // NOTIFY SELLER — shop approved
            // =========================
            NotificationService::send(
                $user,
                'shop_status',
                'Shop approved',
                'Your shop "' . $shop->shop_name . '" has been approved.',
                ['shop_id' => $shop->id]
            );
        }

        return response()->json([
            'success' => true,
            'shop' => $shop
        ]);
    }

    // =========================
    // ADMIN REQUEST CORRECTION
    // =========================
    public function requestCorrection(Request $request, $id)
    {
        if (
            !$request->user()->hasAnyRole([
                'admin',
                'super_admin',
            ])
        ) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found',
            ], 404);
        }

        $shop->update([
            'correction_reason' => $request->reason,
            'correction_requested_at' => now(),
            // status intentionally left as "pending" — the seller stays
            // in the approval queue and can see the reason + resubmit
        ]);

        // =========================
        // NOTIFY SHOP OWNER — correction requested
        // =========================
        NotificationService::send(
            $shop->user,
            'shop_status',
            'Correction requested',
            'Admin requested a correction on your shop: ' . $request->reason,
            ['shop_id' => $shop->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Correction request sent',
            'shop' => $shop,
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

            // =========================
            // NOTIFY SELLER — account + shop created and auto-approved
            // =========================
            NotificationService::send(
                $user,
                'shop_status',
                'Shop created',
                'Your shop "' . $shop->shop_name . '" has been created and approved by admin.',
                ['shop_id' => $shop->id]
            );

            return response()->json([
                'success' => true,
                'user' => $user,
                'shop' => $shop,
            ]);
        });
    }

    // =========================
    // UPDATE SHOP (also used by the seller to resubmit after a
    // correction request — CNIC images are optional here since the
    // seller may only need to fix a text field, not re-upload)
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
            'shop_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'cnic_image' => 'nullable|image|max:2048',
            'cnic_back_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'shop_name' => $request->shop_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'city' => $request->city,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'pending',
            // clear the correction note on resubmit — the old reason
            // shouldn't keep showing once the seller has acted on it
            'correction_reason' => null,
            'correction_requested_at' => null,
        ];

        if ($request->hasFile('cnic_image')) {
            $data['cnic_image'] = $request->file('cnic_image')->store('shops/cnic', 'public');
        }

        if ($request->hasFile('cnic_back_image')) {
            $data['cnic_back_image'] = $request->file('cnic_back_image')->store('shops/cnic', 'public');
        }

        $shop->update($data);

        // =========================
        // NOTIFY ADMINS — seller resubmitted after correction request
        // =========================
        NotificationService::sendToAdmins(
            'shop_pending',
            'Shop resubmitted',
            $shop->shop_name . ' was updated by the seller and needs re-review.',
            ['shop_id' => $shop->id]
        );

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

    public function myShop(Request $request)
   {
       $shop = Shop::where('user_id', $request->user()->id)->first();

      if (!$shop) {
        return response()->json([
            'message' => 'No shop found'
        ], 404);
        }

      return response()->json([
        'shop' => $shop
        ]);
    }

    // =========================
   // SELLER — UPDATE PAYOUT DETAILS
   // =========================
    public function updatePayoutDetails(Request $request)
   {
      $shop = $request->user()->shop()->first();

     if (!$shop) {
         return response()->json([
             'success' => false,
             'message' => 'No shop found for this account',
            ], 404);
        }

     $request->validate([
         'payout_method' => 'required|in:easypaisa,jazzcash',
         'payout_account_number' => 'required|string|max:255',
         'payout_account_name' => 'required|string|max:255',
        ]);

     $shop->update([
         'payout_method' => $request->payout_method,
         'payout_account_number' => $request->payout_account_number,
         'payout_account_name' => $request->payout_account_name,
        ]);

      return response()->json([
         'success' => true,
         'message' => 'Payout details updated',
         'shop' => $shop->fresh(),
        ]);
    }
}
