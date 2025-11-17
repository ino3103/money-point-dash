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
            $adminEmail = env('ADMIN_EMAIL');
            $adminName = env('ADMIN_NAME');
            $adminUserName = env('ADMIN_USER_NAME');
            $adminPassword = env('ADMIN_PASSWORD');

            $errors = [];

            // Check if the admin email is set in the .env file
            if (empty($adminEmail)) {
                $errors[] = 'Admin email is not set in the .env file.';
            }

            // Check if the admin name is set in the .env file
            if (empty($adminName)) {
                $errors[] = 'Admin name is not set in the .env file.';
            }

            // Check if the admin username is set in the .env file
            if (empty($adminUserName)) {
                $errors[] = 'Admin username is not set in the .env file.';
            }

            // Check if the admin password is set in the .env file
            if (empty($adminPassword)) {
                $errors[] = 'Admin password is not set in the .env file.';
            }

            // If any errors occurred, throw an exception
            if (!empty($errors)) {
                throw new \Exception(implode("\n", $errors));
            }

            // Create the Admin role if it doesn't exist
            $adminRole = Role::firstOrCreate(['name' => 'Admin']);

            // Fetch all permissions
            $permissions = Permission::all();

            // Sync all permissions to the Admin role
            $adminRole->syncPermissions($permissions);

            // Create the Admin user if it doesn't exist
            $adminUser = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $adminName,
                    'username' => $adminUserName,
                    'password' => Hash::make($adminPassword),
                ]
            );

            // Assign the Admin role to the Admin user
            $adminUser->assignRole([$adminRole->id]);

            // Commit the transaction if all operations are successful
            \DB::commit();

            // Display success message
            $assignedPermissions = $adminRole->permissions->pluck('name');
            $this->command->info("Admin role assigned the following permissions:\n" . $assignedPermissions->implode("\n"));
            $this->command->info("Admin user created with email: {$adminUser->email}");

            // Display details of all created users vertically
            $allUsers = User::all();
            foreach ($allUsers as $user) {
                $userDetails = "User created with email: {$user->email}, name: {$user->name}, username: {$user->username}";
                $this->command->info($userDetails);
            }
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            \DB::rollBack();

            // Display error message
            $this->command->error($e->getMessage());
        }
    }
}
