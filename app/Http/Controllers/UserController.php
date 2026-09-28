<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private const RESTRICTED_ROLES = ['admin', 'super_admin'];

    public function index()
    {
        return response()->json(
            User::with('roles')
                ->latest()
                ->get()
        );
    }

    public function roles()
    {
        return response()->json(
            Role::select('id', 'name')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

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
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'is_verified' => true,
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user'    => $user->load('roles'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

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

    public function destroy(Request $request, $id)
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


        if ($user->hasRole('admin') && !$request->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only a super admin can delete an admin',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
