<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;

class AuthController extends Controller
{
    // =========================
    // REGISTER (Step 1: send OTP)
    // =========================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $existingUser = User::where('email', $request->email)->first();

        // Already registered AND verified -> block
        if ($existingUser && $existingUser->is_verified) {
            return response()->json([
                'message' => 'Email is already in use',
            ], 422);
        }

        $otp = random_int(100000, 999999);
        $otpExpiry = now()->addMinutes(10);

        if ($existingUser && !$existingUser->is_verified) {
            // exists but never verified -> update details + resend otp
            $existingUser->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiry,
            ]);
            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiry,
                'is_verified' => false,
            ]);

            $user->assignRole('customer');
        }

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json([
            'message' => 'OTP sent to your email. Please verify to complete registration.',
            'email' => $user->email,
        ], 200);
    }

    // =========================
    // VERIFY OTP (Step 2: confirm registration)
    // =========================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Email already verified'], 422);
        }

        if ($user->otp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 422);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired, please request a new one'], 422);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'token' => $token,
            'user' => $user,
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'has_shop' => false,
            'shop_status' => 'none',
            'shop' => null,
        ], 201);
    }

    // =========================
    // RESEND OTP
    // =========================
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Email already verified'], 422);
        }

        $otp = random_int(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json(['message' => 'OTP resent successfully']);
    }
// =========================
// FORGOT PASSWORD (Step 1: send OTP)
// =========================
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'No account found with this email'], 404);
    }

    $otp = random_int(100000, 999999);

    $user->update([
        'otp' => $otp,
        'otp_expires_at' => now()->addMinutes(10),
    ]);

   Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));

    return response()->json([
        'message' => 'OTP sent to your email',
        'email' => $user->email,
    ], 200);
}

// =========================
// RESET PASSWORD (Step 2: verify OTP + update password)
// =========================
public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required',
        'password' => 'required|min:6',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($user->otp != $request->otp) {
        return response()->json(['message' => 'Invalid OTP'], 422);
    }

    if (now()->greaterThan($user->otp_expires_at)) {
        return response()->json(['message' => 'OTP expired, please request a new one'], 422);
    }

    $user->update([
        'password' => Hash::make($request->password),
        'otp' => null,
        'otp_expires_at' => null,
    ]);

    // Optional but good practice: log out old sessions after password reset
    $user->tokens()->delete();

    return response()->json(['message' => 'Password reset successful. Please login.']);
}
    // =========================
    // LOGIN (now blocks unverified users)
    // =========================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->is_verified) {
            return response()->json(['message' => 'Please verify your email first'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        $shop = $user->shop()->first();

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'has_shop' => $shop !== null,
            'shop_status' => $shop->status ?? 'none',
            'shop' => $shop,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $shop = $user->shop()->first();

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'has_shop' => $shop !== null,
            'shop_status' => $shop->status ?? 'none',
            'shop' => $shop,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}