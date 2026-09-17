<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommissionSetting;

class CommissionSettingController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'current_percentage' => CommissionSetting::current(),
        ]);
    }

    public function history(Request $request)
    {
        return response()->json([
            'success' => true,
            'history' => CommissionSetting::with('admin')->latest()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $setting = CommissionSetting::create([
            'percentage' => $request->percentage,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commission updated successfully',
            'setting' => $setting,
        ]);
    }
}