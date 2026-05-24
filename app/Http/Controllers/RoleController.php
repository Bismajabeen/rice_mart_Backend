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

                // USERS COUNT
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

        $role = Role::findOrFail($id);

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
        $role = Role::findOrFail($id);

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    // =========================
    // GET ROLES FOR DROPDOWN
    // =========================

    public function getRoles()
    {
        return response()->json(
            Role::select('id', 'name')->get()
        );
    }

    // =========================
    // GET ALL PERMISSIONS
    // =========================

    public function permissions()
    {
        return response()->json(
            \Spatie\Permission\Models\Permission::select(
                'id',
                'name'
            )->get()
        );
    }

    // =========================
    // ASSIGN PERMISSIONS
    // =========================

    public function assignPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($request->role_id);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned successfully',
        ]);
    }
}