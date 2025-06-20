<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    // Define the permission matrix
    private array $permissionMatrix = [
        'users' => [
            'superadmin' => [ 'index', 'create', 'update', 'delete'],
            'admin' => ['index', 'create', 'update', 'delete'],
            'organizer' => [],
        ],
        'organizations' => [
            'superadmin' => ['index', 'create', 'update', 'delete'],
            'admin' => ['update', 'media'],
            'organizer' => [],
        ],
        'events' => [
            'superadmin' => [],
            'admin' => ['index', 'create', 'update', 'delete'],
            'organizer' => ['index', 'create', 'update'],
        ],
        'ticket-types' => [
            'superadmin' => [],
            'admin' => ['index', 'create', 'update', 'delete'],
            'organizer' => ['index','create', 'update'],
        ],
        'discount-codes' => [
            'superadmin' => [],
            'admin' => ['index', 'create', 'update', 'delete'],
            'organizer' => ['index', 'create', 'update'],
        ],
        'scanner' => [
            'superadmin' => [],
            'admin' => ['show', 'use'],
            'organizer' => ['show', 'use'],
        ],
        'login-as' => [
            'superadmin' => ['use'],
            'admin' => [],
            'organizer' => [],
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
//        $superadmin = Role::where('name', 'superadmin')->first();
//        $superadmin->givePermissionTo(Permission::all());
    }
}
