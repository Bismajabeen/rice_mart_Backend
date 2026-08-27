<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ── REGISTER — Send OTP ───────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|regex:/^[^@]+@[^@]+\.[^@]+$/|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Delete old OTPs
        Otp::where('email', $request->email)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP
        Otp::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP email
        Mail::to($request->email)->send(new OtpMail($otp, $request->name));

        // Save user data in session temporarily
        session([
            'pending_user' => [
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'user',
            ]
        ]);

        return response()->json([
            'message' => 'OTP sent to your email. Please verify.',
            'email'   => $request->email,
        ], 200);
    }

    // ── VERIFY OTP ────────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $otpRecord = Otp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        if (now()->gt($otpRecord->expires_at)) {
            $otpRecord->delete();
            return response()->json(['message' => 'OTP expired. Please register again.'], 400);
        }

        // Create user
        $pendingUser = session('pending_user');

        if (!$pendingUser || $pendingUser['email'] !== $request->email) {
            return response()->json(['message' => 'Session expired. Please register again.'], 400);
        }

        $user = User::create($pendingUser);
        $otpRecord->delete();
        session()->forget('pending_user');

        return response()->json([
            'message' => 'Account created successfully! Please login.',
            'user'    => $user,
        ], 201);
    }

    // ── RESEND OTP ────────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $pendingUser = session('pending_user');

        if (!$pendingUser || $pendingUser['email'] !== $request->email) {
            return response()->json(['message' => 'Session expired. Please register again.'], 400);
        }

        Otp::where('email', $request->email)->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($request->email)->send(new OtpMail($otp, $pendingUser['name']));

        return response()->json(['message' => 'OTP resent successfully.'], 200);
    }

    // ── LOGIN ─────────────────────────────────────────────────
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
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $token = Str::random(60);
        $user->update(['remember_token' => $token]);

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'role'    => $user->role,
            'user'    => $user,
        ], 200);
    }

    // ── LOGOUT ────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if ($user) {
            $user->update(['remember_token' => null]);
        }
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }

    // ── ME ────────────────────────────────────────────────────
    public function me(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        return response()->json(['user' => $user], 200);
    }
}