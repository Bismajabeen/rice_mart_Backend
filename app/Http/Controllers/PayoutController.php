<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellerPayout;

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

        $payout = SellerPayout::findOrFail($id);

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

        return response()->json([
            'success' => true,
            'message' => 'Payout marked as paid',
            'payout' => $payout->fresh(),
        ]);
    }
}