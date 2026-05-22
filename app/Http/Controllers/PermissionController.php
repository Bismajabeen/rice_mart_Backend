<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // GET ALL PERMISSIONS
    public function getPermissions()
    {
        return response()->json(
            Permission::select('id', 'name')->get()
        );
    }

    // ASSIGN PERMISSIONS TO ROLE
    public function assignPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);

        $role = Role::findById($request->role_id);

        $permissions = Permission::whereIn('id', $request->permissions)
            ->pluck('name');

        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully'
        ]);
    }
}