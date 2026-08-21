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
    // ROLES A NON-SUPER-ADMIN IS ALLOWED TO GRANT
    // =========================
    private const RESTRICTED_ROLES = ['admin', 'super_admin'];

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

        // =========================
        // PRIVILEGE ESCALATION GUARD
        // Only a super_admin can grant admin/super_admin roles.
        // =========================
        if (
            in_array($request->role, self::RESTRICTED_ROLES) &&
            !$request->user()->hasRole('super_admin')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Only a super admin can assign this role',
            ], 403);
        }

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

        // =========================
        // PRIVILEGE ESCALATION GUARD
        // Only a super_admin can promote someone to admin/super_admin.
        // =========================
        if (
            in_array($request->role, self::RESTRICTED_ROLES) &&
            !$request->user()->hasRole('super_admin')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Only a super admin can assign this role',
            ], 403);
        }

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

        if (auth()->id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin cannot be deleted',
            ], 403);
        }

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