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
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // 🔥 Spatie role assignment
    $user->assignRole('customer');

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully',
        'token' => $token,
        'user' => $user,
        'roles' => $user->getRoleNames(), // Spatie way
    ]);
}

    // =========================
    // LOGIN
    // =========================
    public function login(Request $request)
{
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    // check approved shop
    $shop = $user->shop()
        ->where('is_approved', 1)
        ->first();

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
        'roles' => $user->getRoleNames(), // 🔥 Spatie roles
        'has_shop' => $shop ? true : false,
        'shop' => $shop,
    ]);
}
}