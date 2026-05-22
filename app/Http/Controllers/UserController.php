<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// use this controller for user management module 

class UserController extends Controller
{
    // =========================
    // GET ALL USERS
    // =========================

    public function index()
    {
        return response()->json(
            User::with('roles')->latest()->get()
        );
    }

    // =========================
    // GET ALL ROLES
    // =========================

    public function roles()
    {
        return response()->json(
            Role::all()
        );
    }

    // =========================
    // CREATE USER
    // =========================

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user->load('roles')
        ]);
    }

    // =========================
    // UPDATE USER
    // =========================

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
        ]);
    }

    // =========================
    // DELETE USER
    // =========================

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin cannot be deleted'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}