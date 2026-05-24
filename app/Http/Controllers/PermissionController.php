<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // =========================
    // GET ALL PERMISSIONS
    // =========================

    public function getPermissions()
    {
        return response()->json(
            Permission::select('id', 'name')->get()
        );
    }

    // =========================
    // GET ROLE PERMISSIONS
    // =========================

      public function getRolePermissions($id)
    {
       $role = Role::findOrFail($id);

      return response()->json([
        'permissions' => $role->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        }),
     ]);
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

        // IMPORTANT FIX
        $role = Role::findOrFail($request->role_id);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned successfully',
        ]);
    }
}