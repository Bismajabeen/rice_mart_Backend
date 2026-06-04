<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // =========================
    // REGISTER
    // =========================

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,

            // IMPORTANT SECURITY FIX
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,

            'user' => $user,

            'roles' => $user->getRoleNames()->values(),

            'permissions' => $user
                ->getAllPermissions()
                ->pluck('name')
                ->values(),

            'has_shop' => false,
            'shop_status' => 'none',
            'shop' => null,
        ], 201);
    }

    // =========================
    // LOGIN
    // =========================

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Remove old tokens
        $user->tokens()->delete();

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        $shop = $user->shop()->first();

        return response()->json([
            'message' => 'Login successful',

            'token' => $token,

            'user' => $user,

            'roles' => $user->getRoleNames()->values(),

            'permissions' => $user
                ->getAllPermissions()
                ->pluck('name')
                ->values(),

            'has_shop' => $shop !== null,

            'shop_status' => $shop->status ?? 'none',

            'shop' => $shop,
        ]);
    }

    // =========================
    // ME
    // =========================

    public function me(Request $request)
    {
        $user = $request->user();

        $shop = $user->shop()->first();

        return response()->json([
            'user' => $user,

            'roles' => $user->getRoleNames()->values(),

            'permissions' => $user
                ->getAllPermissions()
                ->pluck('name')
                ->values(),

            'has_shop' => $shop !== null,

            'shop_status' => $shop->status ?? 'none',

            'shop' => $shop,
        ]);
    }

    // =========================
    // LOGOUT
    // =========================

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}