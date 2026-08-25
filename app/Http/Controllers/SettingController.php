<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function emergencyContact(): JsonResponse
    {
        $email = Setting::where('key', 'super_admin_email')->value('value');
        $phone = Setting::where('key', 'super_admin_phone')->value('value');

        return response()->json([
            'email' => $email,
            'phone' => $phone,
        ]);
    }
}