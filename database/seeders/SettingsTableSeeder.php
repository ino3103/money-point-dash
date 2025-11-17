<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            $settings = [
                // Site Information
                ['key' => 'site_name', 'value' => 'Money Point Dashboard', 'description' => 'The name of the site'],
                ['key' => 'site_logo', 'value' => 'path/to/logo.png', 'description' => 'The logo of the site'],
                ['key' => 'admin_email', 'value' => 'admin@example.com', 'description' => 'Administrator email address'],
                ['key' => 'contact_phone', 'value' => '+1234567890', 'description' => 'Contact phone number'],
                ['key' => 'address', 'value' => '123 Main Street, City, Country', 'description' => 'Company address'],
                ['key' => 'footer_text', 'value' => '© 2024 Money Point Dashboard', 'description' => 'Footer text'],

                // Currency and Localization
                ['key' => 'currency', 'value' => 'TZS', 'description' => 'Default currency'],
                ['key' => 'currency_symbol', 'value' => 'TZS', 'description' => 'Currency symbol'],
                ['key' => 'timezone', 'value' => 'UTC', 'description' => 'Default timezone'],
                ['key' => 'date_format', 'value' => 'Y-m-d', 'description' => 'Default date format'],
                ['key' => 'time_format', 'value' => 'H:i:s', 'description' => 'Default time format'],

                // System Settings
                ['key' => 'maintenance_mode', 'value' => '0', 'description' => 'Maintenance mode (0 for off, 1 for on)'],
                ['key' => 'max_login_attempts', 'value' => '5', 'description' => 'Maximum number of login attempts before lockout'],
                ['key' => 'lockout_duration', 'value' => '30', 'description' => 'Duration of lockout in minutes after maximum login attempts'],
                ['key' => 'back_date', 'value' => '1', 'description' => 'Allow posting data for past days (0 for disabled, 1 for enabled)'],
                ['key' => 'default_page_length', 'value' => '10', 'description' => 'Default page length for datatable'],

                // Backup Settings
                ['key' => 'backup_schedule_hour', 'value' => '12:00 PM', 'description' => 'Hour of the day to perform backup (in format HH:MM AM/PM)'],
                ['key' => 'backup_frequency_days', 'value' => '7', 'description' => 'Create a backup every X days (0 to disable)'],
                ['key' => 'backup_retention_days', 'value' => '30', 'description' => 'Auto delete backups older than X days (set -1 to disable)'],

                // Money Point Specific Settings
                ['key' => 'money_point_low_float_threshold', 'value' => '50000', 'description' => 'Low float alert threshold in currency units. Alert triggers when float balance is below this amount'],
            ];

            $insertedCount = 0;
            $insertedSettings = [];

            foreach ($settings as $setting) {
                $existingSetting = DB::table('settings')->where('key', $setting['key'])->first();

                if (!$existingSetting) {
                    DB::table('settings')->insert($setting);
                    $insertedCount++;
                    $insertedSettings[] = $setting['key'];
                }
            }

            if ($insertedCount > 0) {
                $this->command->info("Inserted {$insertedCount} settings:\n" . implode("\n", $insertedSettings));
            } else {
                $this->command->info("No new settings were inserted.");
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
