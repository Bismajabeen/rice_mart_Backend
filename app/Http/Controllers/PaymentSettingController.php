<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    // =========================
    // GET PAYMENT SETTINGS
    // (used by the checkout screen to show EasyPaisa/JazzCash numbers)
    // =========================
    public function paymentSettings()
    {
        // Single-row settings table. Create a default (empty) row the
        // first time this is called if the admin hasn't set it up yet.
        $settings = PaymentSetting::first();

        if (!$settings) {
            $settings = PaymentSetting::create([
                'easypaisa_number' => '',
                'easypaisa_account_name' => '',
                'jazzcash_number' => '',
                'jazzcash_account_name' => '',
            ]);
        }

        return response()->json([
            'success' => true,
            'settings' => $settings,
        ]);
    }

    // =========================
    // ADMIN UPDATE PAYMENT SETTINGS
    // =========================
    public function adminUpdatePaymentSettings(Request $request)
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
            'easypaisa_number' => 'required|string|max:20',
            'easypaisa_account_name' => 'nullable|string|max:255',
            'jazzcash_number' => 'required|string|max:20',
            'jazzcash_account_name' => 'nullable|string|max:255',
        ]);

        $settings = PaymentSetting::first();

        if (!$settings) {
            $settings = new PaymentSetting();
        }

        $settings->easypaisa_number = $request->easypaisa_number;
        $settings->easypaisa_account_name = $request->easypaisa_account_name;
        $settings->jazzcash_number = $request->jazzcash_number;
        $settings->jazzcash_account_name = $request->jazzcash_account_name;
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment settings updated',
            'settings' => $settings,
        ]);
    }
}