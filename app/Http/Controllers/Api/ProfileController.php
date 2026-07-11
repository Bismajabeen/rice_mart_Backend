<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeleteAccountOtpMail;

class ProfileController extends Controller
{
    // ── GET /api/me ──────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop()->first();

        return response()->json([
            'user'        => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'is_verified' => $user->is_verified,
                'has_shop'    => $shop !== null,
                'roles'       => $user->getRoleNames()->values(),
            ],
            'roles'       => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'has_shop'    => $shop !== null,
            'shop_status' => $shop->status ?? 'none',
            'shop'        => $shop,
        ]);
    }

    // ── PUT /api/update-profile ───────────────────────────────
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        // ── Inline validation ────────────────────────────────
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'sometimes', 'nullable', 'email', 'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Name is required.',
            'email.email'   => 'Please provide a valid email address.',
            'email.unique'  => 'This email is already taken.',
            'password.min'  => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── Always update name ───────────────────────────────
        $user->name = $request->name;

        // ── Update email only if NOT verified ────────────────
        if ($request->filled('email') && !$user->is_verified) {
            $user->email = $request->email;
        }

        // ── Update password if provided ──────────────────────
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);

            // Revoke all other tokens
            $user->tokens()->where(
                'id', '!=', $request->user()->currentAccessToken()->id
            )->delete();
        }

        $user->save();

        $shop = $user->shop()->first();

        return response()->json([
            'message'     => 'Profile updated successfully.',
            'user'        => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'is_verified' => $user->is_verified,
                'has_shop'    => $shop !== null,
                'roles'       => $user->getRoleNames()->values(),
            ],
            'roles'       => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'has_shop'    => $shop !== null,
            'shop_status' => $shop->status ?? 'none',
            'shop'        => $shop,
        ]);
    }

// ── POST /api/delete-account/request ─────────────────────
public function requestDeletion(Request $request): JsonResponse
{
    $user = $request->user();

    $otp = random_int(100000, 999999);

    $user->update([
        'otp'            => $otp,
        'otp_expires_at' => now()->addMinutes(10),
    ]);

    Mail::to($user->email)->send(new DeleteAccountOtpMail($otp, $user->name));

    return response()->json([
        'message' => 'A verification OTP has been sent to your email.',
    ]);
}

// ── POST /api/delete-account/confirm ─────────────────────
public function confirmDeletion(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'otp' => ['required'],
    ], [
        'otp.required' => 'OTP is required.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first(),
        ], 422);
    }

    $user = $request->user();

    if ($user->otp != $request->otp) {
        return response()->json([
            'message' => 'Invalid OTP.',
        ], 422);
    }

    if (now()->greaterThan($user->otp_expires_at)) {
        return response()->json([
            'message' => 'OTP expired. Please request a new one.',
        ], 422);
    }

    // Revoke all tokens first
    $user->tokens()->delete();

    // Hard delete the account
    $user->delete();

    return response()->json([
        'message' => 'Account deleted successfully.',
    ]);
}
}
