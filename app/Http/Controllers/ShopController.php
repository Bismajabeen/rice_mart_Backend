<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\RiceCategory;
use Illuminate\Support\Facades\DB;
use App\Mail\DeleteShopOtpMail;
use Illuminate\Support\Facades\Mail;

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

            'cnic_image' => 'required|image|max:2048',
            'cnic_back_image' => 'required|image|max:2048',
        ]);

        $existingShop = Shop::where('user_id', $request->user()->id)
            ->where('status', '!=', 'removed')
            ->first();

        // Only an in-progress or active shop should actually block a new application.
        if ($existingShop && in_array($existingShop->status, ['pending', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a shop in progress or active. Please update your existing shop instead.',
            ], 400);
        }

        $cnicImagePath = $request->file('cnic_image')->store('shops/cnic', 'public');
        $cnicBackImagePath = $request->file('cnic_back_image')->store('shops/cnic', 'public');

        // A rejected shop reapplying -> reuse the same row instead of creating a duplicate.
        if ($existingShop && $existingShop->status === 'rejected') {
            if ($existingShop->cnic_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingShop->cnic_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($existingShop->cnic_image);
            }
            if ($existingShop->cnic_back_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingShop->cnic_back_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($existingShop->cnic_back_image);
            }

        $existingShop->update([
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
                'correction_reason' => null,
                'correction_requested_at' => null,
                'rejection_reason' => null,
            ]);

            return response()->json([
                'success' => true,
                'shop' => $existingShop->fresh()
            ]);
        }

        // Brand-new applicant, or a reactivated (previously removed) account.
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
            Shop::latest()
                  ->where('is_approved', 0)
                  ->where('status', 'pending')
                  ->get()
        );
    }

    // =========================
    // APPROVED SHOPS
    // =========================
    public function approvedShops()
    {
        return response()->json(
            Shop::latest()
             ->where('is_approved', 1)
             ->where('status', 'approved')
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
            'status' => 'approved',
            'correction_reason' => null,
            'correction_requested_at' => null,
            'rejection_reason' => null,
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
    // REJECT SHOP
    // =========================
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $shop = Shop::findOrFail($id);
        $shop->update([
            'is_approved' => 0,
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'correction_reason' => null,
            'correction_requested_at' => null,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Shop rejected successfully',
            'shop' => $shop
        ]);
    }

    public function rejectedShops()
    {
        return response()->json(
            Shop::latest()->where('status', 'rejected')->get()
        );
    }

    // =========================
    // ADMIN REQUEST CORRECTION
    // =========================
    public function requestCorrection(Request $request, $id)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
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
            'status' => 'pending',
            'is_approved' => 0,
            'correction_reason' => $request->reason,
            'correction_requested_at' => now(),
            'rejection_reason' => null,
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
            'city' => 'required|string|max:100',
            'address' => 'required',
            'cnic' => 'required',
            'description' => 'nullable|string',
            'cnic_image' => 'required|image|max:2048',
            'cnic_back_image' => 'required|image|max:2048',
        ]);

        return DB::transaction(function () use ($request) {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);

            $user->syncRoles(['seller']);

            $cnicImagePath = $request->file('cnic_image')->store('shops/cnic', 'public');
            $cnicBackImagePath = $request->file('cnic_back_image')->store('shops/cnic', 'public');

            $shop = Shop::create([
                'user_id' => $user->id,
                'shop_name' => $request->shop_name,
                'owner_name' => $request->owner_name,
                'phone' => $request->phone,
                'city' => $request->city,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'cnic_image' => $cnicImagePath,
                'cnic_back_image' => $cnicBackImagePath,
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
            'shop_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'cnic' => 'required|string|max:20',
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
            'cnic' => $request->cnic,
            'status' => 'pending',
            'is_approved' => 0,
            'correction_reason' => null,
            'correction_requested_at' => null,
            'rejection_reason' => null,
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
    // SELLER — REQUEST SHOP DELETION (SEND OTP)
    // =========================
    public function requestShopDeletion(Request $request, $id)
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
        $otp = random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(
            new DeleteShopOtpMail($otp, $user->name, $shop->shop_name)
        );

        return response()->json([
            'success' => true,
            'message' => 'A verification OTP has been sent to your email.',
        ]);
    }

    // =========================
    //  SELLER — CONFIRM SHOP DELETION (VERIFY OTP + DELETE)
    //==========================
        public function confirmShopDeletion(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required',
        ]);
        $shop = Shop::findOrFail($id);
        if (
            $shop->user_id != $request->user()->id &&
            !$request->user()->hasRole('admin') &&
            !$request->user()->hasRole('super_admin')
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = $request->user();

        if ($user->otp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['OTP expired. Please request a new one.'], 422);
        }
        // Consume the OTP so it can't be reused for anything else
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $shop->delete();
        $user->syncRoles(['customer']);

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully.',
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
        $shop = Shop::where('user_id', $request->user()->id)
        ->whereNotIn('status', ['removed', 'rejected'])
        ->first();

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
        $shop = $request->user()->shop()->whereNotIn('status', ['removed', 'rejected'])->first();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'No shop found for this account',
            ], 404);
        }

        $request->validate([
            'easypaisa_number' => 'nullable|string|max:255',
            'easypaisa_account_name' => 'nullable|required_with:easypaisa_number|string|max:255',
            'jazzcash_number' => 'nullable|string|max:255',
            'jazzcash_account_name' => 'nullable|required_with:jazzcash_number|string|max:255',
        ]);

        if (!$request->easypaisa_number && !$request->jazzcash_number) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one payout account',
            ], 422);
        }

        $shop->update([
            'payout_easypaisa_number' => $request->easypaisa_number,
            'payout_easypaisa_account_name' => $request->easypaisa_account_name,
            'payout_jazzcash_number' => $request->jazzcash_number,
            'payout_jazzcash_account_name' => $request->jazzcash_account_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payout details updated',
            'shop' => $shop->fresh(),
        ]);
    }
}
