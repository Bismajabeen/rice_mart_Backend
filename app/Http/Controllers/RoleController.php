<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // =========================
    // GET ALL ROLES
    // =========================

    public function index()
    {
        $roles = Role::all();

        $data = $roles->map(function ($role) {

            return [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,

                // users count
                'users_count' => $role->users()->count(),
            ];
        });

        return response()->json($data);
    }

    // =========================
    // CREATE ROLE
    // =========================

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'role' => $role,
        ]);
    }

    // =========================
    // UPDATE ROLE
    // =========================

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $role = Role::findById($id);

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
        ]);
    }

    // =========================
    // DELETE ROLE
    // =========================

    public function destroy($id)
    {
        $role = Role::findById($id);

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    // =========================
    // DROPDOWN ROLES
    // =========================

    public function getRoles()
    {
        return response()->json(
            Role::select('id', 'name')->get()
        );
    }
}