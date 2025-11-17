<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start a database transaction
        \DB::beginTransaction();

        try {
            // Fetch all permissions
            $allPermissions = Permission::all();

            // Define role permissions
            $rolePermissions = [
                'Super Admin' => $allPermissions->pluck('name')->toArray(), // All permissions

                'Treasurer' => [
                    // Profile Management
                    'Edit Own Details',
                    'Change Password',

                    // Money Point - View and Verify
                    'View Money Point Module',
                    'View Shifts',
                    'Verify Shifts',
                    'View Accounts',
                    'View Money Point Transactions',
                    'View Ledger',
                    'View Money Point Reports',
                ],

                'Teller' => [
                    // Profile Management
                    'Edit Own Details',
                    'Change Password',

                    // Money Point - Operational
                    'View Money Point Module',
                    'View Shifts',
                    'Open Shifts',
                    'Submit Shifts',
                    'View Accounts',
                    'View Money Point Transactions',
                    'Create Withdrawals',
                    'Create Deposits',
                ],
            ];

            // Create roles and assign permissions
            foreach ($rolePermissions as $roleName => $permissionNames) {
                // Check if role already exists
                $role = Role::where('name', $roleName)->first();

                if (!$role) {
                    $role = Role::create(['name' => $roleName]);
                    $this->command->info("Created new role: {$roleName}");
                } else {
                    $this->command->info("Role already exists: {$roleName}");
                }

                // Get permission IDs - filter out non-existent permissions
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $foundPermissionNames = $permissions->pluck('name')->toArray();
                $missingPermissions = array_diff($permissionNames, $foundPermissionNames);

                if (!empty($missingPermissions)) {
                    $this->command->warn("  → Missing permissions: " . implode(', ', $missingPermissions));
                }

                // Sync permissions (replaces existing permissions, preventing duplicates)
                $role->syncPermissions($permissions);

                $this->command->info("  → Synced {$permissions->count()} permissions to role: {$roleName}");
            }

            // Create users for each role
            $users = [
                [
                    'name' => 'Super Admin',
                    'email' => 'superadmin@moneypoint.com',
                    'username' => 'superadmin',
                    'password' => Hash::make('password'),
                    'status' => 1, // Active
                    'role' => 'Super Admin',
                ],
                [
                    'name' => 'Treasurer',
                    'email' => 'treasurer@moneypoint.com',
                    'username' => 'treasurer',
                    'password' => Hash::make('password'),
                    'status' => 1, // Active
                    'role' => 'Treasurer',
                ],
                [
                    'name' => 'Teller',
                    'email' => 'teller@moneypoint.com',
                    'username' => 'teller',
                    'password' => Hash::make('password'),
                    'status' => 1, // Active
                    'role' => 'Teller',
                ],
            ];

            foreach ($users as $userData) {
                $roleName = $userData['role'];
                $role = Role::where('name', $roleName)->first();

                if (!$role) {
                    $this->command->warn("Role '{$roleName}' not found. Skipping user creation.");
                    continue;
                }

                // Check if user already exists
                $user = User::where('email', $userData['email'])->first();

                if ($user) {
                    // User exists - update only if needed, but don't overwrite password
                    $updateData = [
                        'name' => $userData['name'],
                        'username' => $userData['username'],
                        'status' => $userData['status'],
                    ];

                    // Only update fields that have changed
                    $needsUpdate = false;
                    foreach ($updateData as $key => $value) {
                        if ($user->$key != $value) {
                            $needsUpdate = true;
                            break;
                        }
                    }

                    if ($needsUpdate) {
                        $user->update($updateData);
                        $this->command->info("Updated existing user: {$user->name} ({$user->email})");
                    } else {
                        $this->command->info("User already exists: {$user->name} ({$user->email})");
                    }
                } else {
                    // User doesn't exist - create new user
                    unset($userData['role']);
                    $user = User::create($userData);
                    $this->command->info("Created new user: {$user->name} ({$user->email})");
                }

                // Sync roles to prevent duplicates (replaces existing roles with the specified one)
                $currentRoles = $user->roles->pluck('name')->toArray();
                $user->syncRoles([$roleName]);

                if (in_array($roleName, $currentRoles)) {
                    $this->command->info("  → User already has role: {$roleName} (synced to ensure no duplicates)");
                } else {
                    $this->command->info("  → Assigned role: {$roleName}");
                }
            }

            // Commit the transaction if all operations are successful
            \DB::commit();

            $this->command->info("\n=== Summary ===");
            $this->command->info("Roles created: " . implode(', ', array_keys($rolePermissions)));
            $this->command->info("\nUsers created:");
            foreach ($users as $userData) {
                $this->command->info("  - {$userData['name']} ({$userData['email']}) - Role: {$userData['role']}");
            }
            $this->command->info("\nDefault password for all users: password");
            $this->command->warn("Please change the default passwords after first login!");
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            \DB::rollBack();

            // Display error message
            $this->command->error("Error occurred: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
