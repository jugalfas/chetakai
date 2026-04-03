<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::with('roles')
            ->whereNotIn('role', ['super_admin'])
            ->when($request->search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });

        return Inertia::render('Admin/Admins/Index', [
            'admins' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => ['search' => $request->search],
            'roles' => Role::where('guard_name', 'admin')->whereNotIn('name', ['super_admin'])->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users',
            'role' => 'required|exists:roles,name',
        ]);

        $admin = Admin::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(32)),
            'status' => 'active',
            'role' => $validated['role'], // To satisfy explicit role column (optional but good practice to fill)
            'created_by' => auth('admin')->id(),
        ]);

        $admin->assignRole($validated['role']);

        $token = \Illuminate\Support\Facades\Password::broker('admins')->createToken($admin);
        $admin->notify(new \App\Notifications\AdminInvitationNotification($token, $admin->role));

        activity()
            ->performedOn($admin)
            ->causedBy(auth('admin')->user())
            ->log('invited admin user');

        return redirect()->route('admin.admins.index')->with('success', 'Admin user invited successfully.');
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'role' => 'sometimes|exists:roles,name',
            'status' => 'sometimes|in:active,suspended',
        ]);

        if (isset($validated['status'])) {
            if ($admin->id === auth('admin')->id()) {
                return back()->with('error', 'You cannot suspend your own account.');
            }
            $admin->update(['status' => $validated['status']]);

            activity()
                ->performedOn($admin)
                ->causedBy(auth('admin')->user())
                ->log("changed admin status to {$validated['status']}");
        }

        if (isset($validated['role'])) {
            $admin->update(['role' => $validated['role']]);
            $admin->syncRoles([$validated['role']]);

            activity()
                ->performedOn($admin)
                ->causedBy(auth('admin')->user())
                ->log("changed admin role to {$validated['role']}");
        }

        return redirect()->route('admin.admins.index')->with('success', 'Admin user updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        activity()
            ->performedOn($admin)
            ->causedBy(auth('admin')->user())
            ->log('deleted admin user');

        return redirect()->route('admin.admins.index')->with('success', 'Admin user deleted successfully.');
    }
}
