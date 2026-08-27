<?php

namespace App\Http\Controllers;

use App\Mail\SellerRemovedMail;
use App\Models\BannedEmail;
use App\Models\Product;
use App\Models\SellerRemoval;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SellerRemovalController extends Controller
{
    // =========================
    // PERMANENTLY REMOVE SELLER
    // =========================
    public function remove(Request $request, $shopId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'permanently_ban' => 'nullable|boolean',
        ]);

        $shop = Shop::with('user')->findOrFail($shopId);
        $user = $shop->user;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user linked to this shop',
            ], 404);
        }

        $permanentlyBan = (bool) $request->boolean('permanently_ban');

        DB::transaction(function () use ($request, $shop, $user, $permanentlyBan) {

            // 1. Mark shop removed (hidden from customers)
            $shop->update([
                'status' => 'removed',
            ]);

            // 2. Deactivate all products under this shop (hidden from customers)
            Product::where('shop_id', $shop->id)->update(['is_active' => false]);

            // 3. Revoke seller role, deactivate account (soft — not deleted)
            $user->syncRoles(['customer']);
            $user->update([
                'account_status' => 'removed',
                'removed_reason' => $request->reason,
                'removed_at' => now(),
            ]);

            // 4. Kill all active sessions/tokens immediately
            $user->tokens()->delete();

            // 5. Optional permanent ban
            if ($permanentlyBan) {
                BannedEmail::updateOrCreate(
                    ['email' => $user->email],
                    ['reason' => $request->reason, 'banned_by' => auth()->id()]
                );
            }

            // 6. Audit trail
            SellerRemoval::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'removed_by' => auth()->id(),
                'reason' => $request->reason,
                'permanently_banned' => $permanentlyBan,
            ]);
        });

        // 7. Notify the seller by email (outside the transaction is fine here)
        try {
            Mail::to($user->email)->send(
                new SellerRemovedMail($user->name, $shop->shop_name, $request->reason)
            );
        } catch (\Throwable $e) {
            // Don't fail the whole request if email sending has an issue —
            // the removal itself already succeeded.
        }

        return response()->json([
            'success' => true,
            'message' => 'Seller removed successfully',
        ]);
    }

    // =========================
    // REMOVED SHOPS (for record-keeping tab)
    // =========================
    public function removedShops()
    {
        return response()->json(
            Shop::latest()->where('status', 'removed')->get()
        );
    }
}