<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
}