<?php

namespace Database\Seeders;

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

        // Create permissions for Money Point System

        // Branch Management Permissions
        $branchPermissions = [
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',
        ];

        // User Management Permissions
        $userPermissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
        ];

        // Role & Permission Management
        $rolePermissions = [
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'assign permissions',
        ];

        // Float Provider Management
        $floatProviderPermissions = [
            'view float providers',
            'create float providers',
            'edit float providers',
            'delete float providers',
        ];

        // Float Allocation (Treasurer)
        $allocationPermissions = [
            'view allocations',
            'create allocations',
            'edit allocations',
            'delete allocations',
            'approve allocations',
        ];

        // Account Management
        $accountPermissions = [
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
        ];

        // Shift Management
        $shiftPermissions = [
            'view shifts',
            'open shifts',
            'close shifts',
            'verify shifts',
            'reject shifts',
            'view own shifts',
            'close own shifts',
        ];

        // Transaction Management
        $transactionPermissions = [
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'view own transactions',
            'create own transactions',
        ];

        // Reports & Analytics
        $reportPermissions = [
            'view reports',
            'view shift reports',
            'view transaction reports',
            'view float reports',
            'view variance reports',
            'view daily summary',
            'view teller performance',
            'export reports',
        ];

        // Dashboard Access
        $dashboardPermissions = [
            'view dashboard',
            'view treasurer dashboard',
            'view branch manager dashboard',
            'view cashier dashboard',
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $branchPermissions,
            $userPermissions,
            $rolePermissions,
            $floatProviderPermissions,
            $allocationPermissions,
            $accountPermissions,
            $shiftPermissions,
            $transactionPermissions,
            $reportPermissions,
            $dashboardPermissions
        );

        // Create all permissions (prevent duplicates)
        $createdPermissions = 0;
        $existingPermissions = 0;
        foreach ($allPermissions as $permission) {
            $existing = Permission::where('name', $permission)
                ->where('guard_name', 'web')
                ->first();

            if (!$existing) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
                $createdPermissions++;
            } else {
                $existingPermissions++;
            }
        }

        // Create Roles (prevent duplicates)
        $roles = [
            'Super Admin',
            'Treasurer',
            'Branch Manager',
            'Cashier'
        ];

        $createdRoles = 0;
        $existingRoles = 0;
        foreach ($roles as $roleName) {
            $existing = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if (!$existing) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $createdRoles++;
            } else {
                $existingRoles++;
            }
        }

        $this->command->info('Roles and Permissions processed successfully!');
        $this->command->info('Roles: Super Admin, Treasurer, Branch Manager, Cashier');
        $this->command->info("Permissions: Created {$createdPermissions}, Already existed: {$existingPermissions}");
        $this->command->info("Roles: Created {$createdRoles}, Already existed: {$existingRoles}");
        $this->command->info('Note: Permissions are not assigned to roles. Admin can assign them dynamically through the UI.');
    }
}
