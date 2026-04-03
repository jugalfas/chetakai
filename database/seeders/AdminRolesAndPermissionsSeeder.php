<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Admin;

class AdminRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $guard = 'admin';

        $modules = [
            'Dashboard' => [
                'dashboard.view' => 'View dashboard',
            ],
            'Users' => [
                'users.view' => 'View users list',
                'users.create' => 'Create user',
                'users.edit' => 'Edit user details',
                'users.suspend' => 'Suspend / ban user',
                'users.impersonate' => 'Login as user',
                'users.change_plan' => 'Change user plan',
                'users.reset_usage' => 'Reset post usage',
                'users.reset_otp' => 'Send OTP reset',
                'users.delete' => 'Delete user permanently',
            ],
            'Subscription plans' => [
                'plans.view' => 'View plans',
                'plans.create' => 'Create plan',
                'plans.edit' => 'Edit plan',
                'plans.delete' => 'Delete plan',
            ],
            'Prompt templates' => [
                'prompts.view' => 'View prompt templates',
                'prompts.create' => 'Create prompt template',
                'prompts.edit' => 'Edit prompt template',
                'prompts.delete' => 'Delete prompt template',
            ],
            'Content types' => [
                'content_types.view' => 'View content types',
                'content_types.manage' => 'Create / edit / delete content types',
            ],
            'Platforms & integrations' => [
                'platforms.view' => 'View platforms',
                'platforms.manage' => 'Manage platform integrations',
            ],
            'Email templates' => [
                'email_templates.view' => 'View email templates',
                'email_templates.manage' => 'Edit & toggle email templates',
            ],
            'Audit logs' => [
                'audit_logs.view' => 'View audit logs',
                'audit_logs.export' => 'Export audit logs',
            ],
            'Platform settings' => [
                'settings.view' => 'View platform settings',
                'settings.manage' => 'Edit platform settings',
            ],
            'Admin users' => [
                'admin_users.view' => 'View admin users',
                'admin_users.create' => 'Invite new admin',
                'admin_users.edit' => 'Edit admin role & status',
            ],
            'Roles & permissions' => [
                'roles.manage' => 'Manage role permissions',
            ],
        ];

        foreach ($modules as $moduleName => $perms) {
            foreach ($perms as $key => $label) {
                Permission::firstOrCreate(['name' => $key, 'guard_name' => $guard]);
            }
        }

        // CREATE SUPER ADMIN
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
        $superAdminRole->syncPermissions(Permission::where('guard_name', $guard)->get());

        // CREATE ADMIN
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $adminRole->syncPermissions([
            'dashboard.view',
            'users.view','users.create','users.edit','users.suspend','users.impersonate','users.change_plan','users.reset_usage','users.reset_otp','users.delete',
            'plans.view','plans.create','plans.edit','plans.delete',
            'prompts.view','prompts.create','prompts.edit','prompts.delete',
            'content_types.view','content_types.manage',
            'platforms.view','platforms.manage',
            'email_templates.view','email_templates.manage',
            'audit_logs.view','audit_logs.export',
            'settings.view','settings.manage',
        ]);

        // CREATE CONTENT MANAGER
        $contentManagerRole = Role::firstOrCreate(['name' => 'content_manager', 'guard_name' => $guard]);
        $contentManagerRole->syncPermissions([
            'dashboard.view',
            'prompts.view','prompts.create','prompts.edit','prompts.delete',
            'content_types.view','content_types.manage',
        ]);

        // CREATE SUPPORT
        $supportRole = Role::firstOrCreate(['name' => 'support', 'guard_name' => $guard]);
        $supportRole->syncPermissions([
            'dashboard.view',
            'users.view','users.edit','users.suspend','users.impersonate','users.change_plan','users.reset_usage','users.reset_otp',
            'audit_logs.view',
        ]);

        // Ensure super admin retains its role
        $adminUser = Admin::where('email', 'admin@chetak.io')->first() ?? Admin::first();
        if ($adminUser) {
            $adminUser->update(['role' => 'super_admin']);
            $adminUser->syncRoles(['super_admin']);
        }
    }
}
