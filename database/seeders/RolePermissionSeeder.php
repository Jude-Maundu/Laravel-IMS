<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Inventory
            'view inventory',
            'create inventory',
            'edit inventory',
            'delete inventory',
            // Reports
            'view reports',
            'generate reports',
            // Assignments
            'assign items',
            'return items',
            // Users (admin only)
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);

        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        // Manager gets selected permissions (no user management, no delete)
        $manager->syncPermissions([
            'view inventory',
            'create inventory',
            'edit inventory',
            'view reports',
            'generate reports',
            'assign items',
            'return items',
        ]);
    }
}

