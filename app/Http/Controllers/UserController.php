<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // =========================
    // GET ALL USERS
    // =========================

    public function index()
    {
        return response()->json(
            User::with('roles')
                ->latest()
                ->get()
        );
    }

    // =========================
    // GET ALL ROLES
    // =========================

    public function roles()
    {
        return response()->json(
            Role::select('id', 'name')->get()
        );
    }

    // =========================
    // CREATE USER
    // =========================

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user'    => $user->load('roles'),
        ], 201);
    }

    // =========================
    // UPDATE USER
    // =========================

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Protect Super Admin
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin cannot be modified',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user'    => $user->load('roles'),
        ]);
    }

    // =========================
    // DELETE USER
    // =========================

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if (auth()->id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        // Protect Super Admin
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin cannot be deleted',
            ], 403);
        }

        // Protect Admin
        if ($user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin cannot be deleted',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}