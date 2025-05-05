<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    // Define the permission matrix
    private array $permissionMatrix = [
        'users' => [
            'superadmin' => ['create', 'read', 'update', 'delete'],
            'admin' => ['create', 'read', 'update', 'delete'],
            'organizer' => ['read'],
        ],
        'organizations' => [
            'superadmin' => ['create', 'read', 'update', 'delete'],
            'admin' => ['read', 'update'],
            'organizer' => ['read'],
        ],
        'events' => [
            'superadmin' => ['create', 'read', 'update', 'delete'],
            'admin' => ['create', 'read', 'update', 'delete'],
            'organizer' => ['create', 'read', 'update', 'delete'],
        ],
        'discount codes' => [
            'superadmin' => ['create', 'read', 'update', 'delete'],
            'admin' => ['create', 'read', 'update', 'delete'],
            'organizer' => ['create', 'read', 'update', 'delete'],
        ],
        'ticket types' => [
            'superadmin' => ['create', 'read', 'update', 'delete'],
            'admin' => ['create', 'read', 'update', 'delete'],
            'organizer' => ['create', 'read', 'update', 'delete'],
        ],
        'tickets' => [
            'superadmin' => ['create', 'read', 'update', 'delete'],
            'admin' => ['create', 'read', 'update', 'delete'],
            'organizer' => ['read'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions and assign to roles
        foreach ($this->permissionMatrix as $model => $roles) {
            foreach ($roles as $roleName => $actions) {
                // Ensure the role exists
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);

                // Create permissions and assign to role
                foreach ($actions as $action) {
                    $permissionName = "{$model}.{$action}";
                    $permission = Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'web'
                    ]);

                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }

        // Additional global permissions if needed
        $superadmin = Role::where('name', 'superadmin')->first();
        $superadmin->givePermissionTo(Permission::all());
    }
}
