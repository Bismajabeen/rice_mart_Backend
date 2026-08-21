<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellerPayout;
use App\Services\NotificationService;

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

        return response()->json([
            'success' => true,
            'payouts' => SellerPayout::with(['order', 'shop', 'admin'])
                ->latest()
                ->get(),
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
}
