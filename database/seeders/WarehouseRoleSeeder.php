<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WarehouseRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Warehouse role
        $warehouse = Role::firstOrCreate(['name' => 'Warehouse']);

        // Create permissions needed for warehouse role
        Permission::firstOrCreate(['name' => 'access scan sessions']);
        Permission::firstOrCreate(['name' => 'submit scans']);

        // Assign permissions to role
        $warehouse->givePermissionTo(['access scan sessions', 'submit scans']);

        $this->command->info('✓ Warehouse role and permissions created successfully.');
    }
}
