<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ── REGISTER ─────────────────────────────
    public function register(Request $request)
    {
        // Basic validation (keep simple for FYP)
        if (
            !$request->name ||
            !$request->username ||
            !$request->email ||
            !$request->password
        ) {
            return response()->json([
                'success' => false,
                'message' => 'All fields are required'
            ], 400);
        }

        if ($request->password !== $request->password_confirmation) {
            return response()->json([
                'success' => false,
                'message' => 'Passwords do not match'
            ], 400);
        }

        // Check existing user
        $exists = User::where('email', $request->email)->first();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User already exists'
            ], 400);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully',
            'user' => $user
        ], 201);
    }

    // ── LOGIN ───────────────────────────────
    public function login(Request $request)
    {
        if (!$request->email || !$request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password required'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Revoke previous tokens (optional but recommended)
        $user->tokens()->delete();

        // Generate a long-lived Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $token,
            'token_type' => 'Bearer',
            'user'    => $user
        ], 200);
    }
}
