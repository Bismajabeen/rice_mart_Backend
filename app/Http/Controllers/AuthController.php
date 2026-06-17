<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        return response()->json([
            'message' => 'Account created successfully.',
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
                    ->where('email', $request->email)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // ← FIX: update ki jagah save use kiya
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'role'    => $user->role,
            'user'    => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }

    public function me(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        return response()->json(['user' => $user], 200);
    }
}