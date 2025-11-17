<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            $permissions = [
                // Users Management
                ['name' => 'View Users', 'category' => 'Users', 'guard_name' => 'web'],
                ['name' => 'Create Users', 'category' => 'Users', 'guard_name' => 'web'],
                ['name' => 'Edit Users', 'category' => 'Users', 'guard_name' => 'web'],
                ['name' => 'Delete Users', 'category' => 'Users', 'guard_name' => 'web'],
                ['name' => 'Change User Status', 'category' => 'Users', 'guard_name' => 'web'],

                // Roles Management
                ['name' => 'View Roles', 'category' => 'Roles', 'guard_name' => 'web'],
                ['name' => 'Create Roles', 'category' => 'Roles', 'guard_name' => 'web'],
                ['name' => 'Edit Roles', 'category' => 'Roles', 'guard_name' => 'web'],
                ['name' => 'Delete Roles', 'category' => 'Roles', 'guard_name' => 'web'],

                // Profile Management
                ['name' => 'Edit Own Details', 'category' => 'Profile', 'guard_name' => 'web'],
                ['name' => 'Change Password', 'category' => 'Profile', 'guard_name' => 'web'],

                // Settings Management
                ['name' => 'View Settings Module', 'category' => 'Settings', 'guard_name' => 'web'],
                ['name' => 'View System Settings', 'category' => 'Settings', 'guard_name' => 'web'],
                ['name' => 'Edit System Settings', 'category' => 'Settings', 'guard_name' => 'web'],
                ['name' => 'View Email Settings', 'category' => 'Settings', 'guard_name' => 'web'],
                ['name' => 'Edit Email Settings', 'category' => 'Settings', 'guard_name' => 'web'],
                ['name' => 'View SMS Settings', 'category' => 'Settings', 'guard_name' => 'web'],
                ['name' => 'Edit SMS Settings', 'category' => 'Settings', 'guard_name' => 'web'],

                // Money Point Permissions
                ['name' => 'View Money Point Module', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'View Shifts', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'Open Shifts', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'Submit Shifts', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'Verify Shifts', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'View Accounts', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'Create Accounts', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'View Money Point Transactions', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'Create Withdrawals', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'Create Deposits', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'View Ledger', 'category' => 'Money Point', 'guard_name' => 'web'],
                ['name' => 'View Money Point Reports', 'category' => 'Money Point', 'guard_name' => 'web'],
            ];

            $insertedCount = 0;
            $insertedPermissions = [];

            foreach ($permissions as $permission) {
                $existingPermission = DB::table('permissions')->where('name', $permission['name'])->first();

                if (!$existingPermission) {
                    DB::table('permissions')->insert($permission);
                    $insertedCount++;
                    $insertedPermissions[] = $permission['name'];
                }
            }

            if ($insertedCount > 0) {
                $this->command->info("Inserted {$insertedCount} permissions:\n" . implode("\n", $insertedPermissions));
            } else {
                $this->command->info("No new permissions were inserted.");
            }

            // Commit the transaction if all operations are successful
            DB::commit();
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            DB::rollBack();

            // Display error message
            $this->command->error("Error occurred: " . $e->getMessage());
        }
    }
}
