<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    /**
     * Assign role to user (prevent duplicates)
     */
    public function assignRoleToUser(User $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
        
        if (!$role) {
            throw new \Exception("Role '{$roleName}' not found.");
        }

        // Check if user already has this role
        if ($user->hasRole($roleName)) {
            return false; // Already assigned
        }

        $user->assignRole($role);
        return true; // Successfully assigned
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(User $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
        
        if (!$role) {
            throw new \Exception("Role '{$roleName}' not found.");
        }

        // Check if user has this role
        if (!$user->hasRole($roleName)) {
            return false; // Not assigned
        }

        $user->removeRole($role);
        return true; // Successfully removed
    }

    /**
     * Assign permission to role (prevent duplicates)
     */
    public function assignPermissionToRole(Role $role, string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->where('guard_name', 'web')->first();
        
        if (!$permission) {
            throw new \Exception("Permission '{$permissionName}' not found.");
        }

        // Check if role already has this permission
        if ($role->hasPermissionTo($permissionName)) {
            return false; // Already assigned
        }

        $role->givePermissionTo($permission);
        return true; // Successfully assigned
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(Role $role, string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->where('guard_name', 'web')->first();
        
        if (!$permission) {
            throw new \Exception("Permission '{$permissionName}' not found.");
        }

        // Check if role has this permission
        if (!$role->hasPermissionTo($permissionName)) {
            return false; // Not assigned
        }

        $role->revokePermissionTo($permission);
        return true; // Successfully removed
    }

    /**
     * Assign multiple permissions to role (prevent duplicates)
     */
    public function assignPermissionsToRole(Role $role, array $permissionNames): array
    {
        $results = [
            'assigned' => [],
            'already_assigned' => [],
            'not_found' => [],
        ];

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::where('name', $permissionName)->where('guard_name', 'web')->first();
            
            if (!$permission) {
                $results['not_found'][] = $permissionName;
                continue;
            }

            if ($role->hasPermissionTo($permissionName)) {
                $results['already_assigned'][] = $permissionName;
                continue;
            }

            $role->givePermissionTo($permission);
            $results['assigned'][] = $permissionName;
        }

        return $results;
    }

    /**
     * Create role (prevent duplicates)
     */
    public function createRole(string $roleName): Role
    {
        $existing = Role::where('name', $roleName)->where('guard_name', 'web')->first();
        
        if ($existing) {
            throw new \Exception("Role '{$roleName}' already exists.");
        }

        return Role::create(['name' => $roleName, 'guard_name' => 'web']);
    }

    /**
     * Create permission (prevent duplicates)
     */
    public function createPermission(string $permissionName): Permission
    {
        $existing = Permission::where('name', $permissionName)->where('guard_name', 'web')->first();
        
        if ($existing) {
            throw new \Exception("Permission '{$permissionName}' already exists.");
        }

        return Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
    }

    /**
     * Check if user can be assigned a role (prevent duplicate super admin)
     */
    public function canAssignRole(User $user, string $roleName): bool
    {
        // Prevent creating multiple Super Admin users
        if ($roleName === 'Super Admin') {
            $existingSuperAdmin = User::role('Super Admin')->where('id', '!=', $user->id)->first();
            if ($existingSuperAdmin) {
                return false; // Super Admin already exists
            }
        }

        return true;
    }
}

