<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Super Admin role exists, if not create it
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first();

        if (!$superAdminRole) {
            $superAdminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
            $this->command->info('Super Admin role created.');
        } else {
            $this->command->info('Super Admin role already exists.');
        }

        // Get Super Admin credentials from environment variables
        $superAdminName = env('SUPER_ADMIN_NAME', 'Super Admin');
        $superAdminEmail = env('SUPER_ADMIN_EMAIL', 'admin@moneypoint.com');
        $superAdminPassword = env('SUPER_ADMIN_PASSWORD', 'password');

        // Validate required environment variables
        if ($superAdminEmail === 'admin@moneypoint.com' || empty($superAdminPassword)) {
            $this->command->warn('⚠️  Warning: Using default Super Admin credentials. Please set SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD in .env file.');
        }

        // Check if Super Admin user already exists
        $superAdmin = User::where('email', $superAdminEmail)->first();

        if (!$superAdmin) {
            // Create Super Admin user
            $superAdmin = User::create([
                'name' => $superAdminName,
                'email' => $superAdminEmail,
                'password' => Hash::make($superAdminPassword),
                'email_verified_at' => now(),
            ]);
            $this->command->info('Super Admin user created.');
        } else {
            $this->command->warn('Super Admin user already exists. Skipping creation.');
        }

        // Assign Super Admin role (prevent duplicate assignment)
        if ($superAdmin && !$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole($superAdminRole);
            $this->command->info('Super Admin role assigned to user.');
        } else {
            $this->command->info('Super Admin role already assigned to user.');
        }

        // Give Super Admin all permissions (prevent duplicate assignments)
        $allPermissions = \Spatie\Permission\Models\Permission::all();
        $currentPermissionIds = $superAdminRole->permissions->pluck('id')->toArray();
        $newPermissions = $allPermissions->filter(function ($permission) use ($currentPermissionIds) {
            return !in_array($permission->id, $currentPermissionIds);
        });

        if ($newPermissions->count() > 0) {
            $superAdminRole->givePermissionTo($newPermissions);
            $this->command->info("Granted {$newPermissions->count()} new permissions to Super Admin role.");
        } else {
            $this->command->info('Super Admin role already has all permissions.');
        }

        $this->command->info('');
        $this->command->info('Super Admin Setup Complete!');
        $this->command->info("Name: {$superAdminName}");
        $this->command->info("Email: {$superAdminEmail}");
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
