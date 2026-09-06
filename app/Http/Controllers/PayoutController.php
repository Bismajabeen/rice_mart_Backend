<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellerPayout;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;

class PayoutController extends Controller
{
    // =========================
    // ADMIN — LIST ALL PAYOUTS
    // =========================
    public function index(Request $request)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payouts = SellerPayout::with(['order', 'shop', 'admin'])
            ->latest()
            ->get();
        $payouts->each(function ($payout) {
            $payout->proof_url = $payout->proof_path
            ? asset(Storage::url($payout->proof_path))
            : null;
        });

        return response()->json([
            'success' => true,
            'payouts' => $payouts,
        ]);
    }

    // =========================
    // ADMIN — MARK A PAYOUT AS PAID
    // =========================
    public function pay(Request $request, $id)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payout = SellerPayout::with('shop')->findOrFail($id);

        if ($payout->status !== 'ready') {
            return response()->json([
                'success' => false,
                'message' => 'This payout is not ready yet — the customer hasn\'t confirmed receipt.',
            ], 400);
        }

        $request->validate([
            'payout_method' => 'required|in:easypaisa,jazzcash',
            'transaction_id' => 'required|string|max:255',
            'proof' => 'required|image|max:2048',
        ]);
        // Block payout if the seller hasn't added the account for the chosen method
        $shop = $payout->shop;

        $sellerHasEasypaisa = !empty($shop->payout_easypaisa_number);
        $sellerHasJazzcash = !empty($shop->payout_jazzcash_number);

        if ($request->payout_method === 'easypaisa' && !$sellerHasEasypaisa) {
            return response()->json([
                'success' => false,
                'message' => 'The seller has not provided an Easypaisa account number.',
            ], 400);
        }

        if ($request->payout_method === 'jazzcash' && !$sellerHasJazzcash) {
            return response()->json([
                'success' => false,
                'message' => 'The seller has not provided a JazzCash account number.',
            ], 400);
        }

        $proofPath = $request->file('proof')->store('payouts', 'public');

        $payout->update([
            'status' => 'paid',
            'payout_method' => $request->payout_method,
            'transaction_id' => $request->transaction_id,
            'proof_path' => $proofPath,
            'paid_at' => now(),
            'paid_by' => $request->user()->id,
        ]);

        // =========================
        // NOTIFY SELLER — payout sent
        // =========================
        $shop = $payout->shop;

        if ($shop && $shop->user) {
            NotificationService::send(
                $shop->user,
                'payout_paid',
                'Payout sent',
                'Your payout of Rs ' . number_format($payout->net_amount, 2) . ' has been sent via ' . $request->payout_method . '.',
                ['payout_id' => $payout->id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Payout marked as paid',
            'payout' => $payout->fresh(),
        ]);
    }

    // =========================
    // SELLER — LIST THEIR OWN PAYOUTS
   // =========================
    public function sellerPayouts(Request $request)
    {
      $shop = $request->user()->shop()->first();

      if (!$shop) {
         return response()->json(['success' => false, 'message' => 'No shop found'], 404);
        }

       $payouts = SellerPayout::with('order')
            ->where('shop_id', $shop->id)
            ->latest()
            ->get();

        $payouts->each(function ($payout) {
            $payout->proof_url = $payout->proof_path
            ? asset(Storage::url($payout->proof_path))
            : null;
        });

      return response()->json([
         'success' => true,
         'payouts' => $payouts,
        ]);

    }
}
