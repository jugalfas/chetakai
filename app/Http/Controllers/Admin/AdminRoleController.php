<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Models\Admin;

class AdminRoleController extends Controller
{
    public function index()
    {
        $guard = 'admin';
        
        $roles = Role::where('guard_name', $guard)->with('permissions')->get();

        $adminCounts = Admin::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'adminCounts' => $adminCounts
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super admin capabilities are hardcoded and cannot be modified.');
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', ucfirst(str_replace('_', ' ', $role->name)) . ' permissions updated successfully.');
    }
}
