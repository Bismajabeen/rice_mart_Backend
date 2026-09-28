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

            $shop->update([
                'status' => 'removed',
            ]);
 
            Product::where('shop_id', $shop->id)->update(['is_active' => false]);

            $user->syncRoles(['customer']);
            $user->update([
                'account_status' => 'removed',
                'removed_reason' => $request->reason,
                'removed_at' => now(),
            ]);

            
            $user->tokens()->delete();

            if ($permanentlyBan) {
                BannedEmail::updateOrCreate(
                    ['email' => $user->email],
                    ['reason' => $request->reason, 'banned_by' => auth()->id()]
                );
            }

            SellerRemoval::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'removed_by' => auth()->id(),
                'reason' => $request->reason,
                'permanently_banned' => $permanentlyBan,
            ]);
        });

        try {
            Mail::to($user->email)->send(
                new SellerRemovedMail($user->name, $shop->shop_name, $request->reason)
            );
        } catch (\Throwable $e) {
            
        }

        return response()->json([
            'success' => true,
            'message' => 'Seller removed successfully',
        ]);
    }

    public function removedShops()
    {
        return response()->json(
            Shop::latest()->where('status', 'removed')->get()
        );
    }
}