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
    public function run()
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            $settings = [
                ['key' => 'site_name', 'value' => 'Saccoss Management System', 'description' => 'The name of the site'],
                ['key' => 'site_logo', 'value' => 'path/to/logo.png', 'description' => 'The logo of the site'],
                ['key' => 'admin_email', 'value' => 'admin@example.com', 'description' => 'Administrator email address'],
                ['key' => 'sending_sms', 'value' => 'enabled', 'description' => 'Enable or Disable Sending SMS'],
                ['key' => 'entry_fee', 'value' => 50000, 'description' => 'Members Entry Fee'],
                ['key' => 'loan_fee', 'value' => 10000, 'description' => 'Loan Fee'],
                ['key' => 'share_price', 'value' => 50000, 'description' => 'Price Per Share'],
                ['key' => 'contact_phone', 'value' => '+1234567890', 'description' => 'Contact phone number'],
                ['key' => 'address', 'value' => '123 Main Street, City, Country', 'description' => 'Company address'],
                ['key' => 'currency', 'value' => 'TZS', 'description' => 'Default currency'],
                ['key' => 'currency_symbol', 'value' => 'TZS', 'description' => 'Default currency'],
                ['key' => 'timezone', 'value' => 'UTC', 'description' => 'Default timezone'],
                ['key' => 'date_format', 'value' => 'Y-m-d', 'description' => 'Default date format'],
                ['key' => 'time_format', 'value' => 'H:i:s', 'description' => 'Default time format'],
                ['key' => 'footer_text', 'value' => '© 2024 Saccoss Management System', 'description' => 'Footer text'],
                ['key' => 'maintenance_mode', 'value' => '0', 'description' => 'Maintenance mode (0 for off, 1 for on)'],
                ['key' => 'max_login_attempts', 'value' => '5', 'description' => 'Maximum number of login attempts before lockout'],
                ['key' => 'lockout_duration', 'value' => '30', 'description' => 'Duration of lockout in minutes after maximum login attempts'],
                ['key' => 'user_registration', 'value' => '1', 'description' => 'Allow user registration (0 for no, 1 for yes)'],
                ['key' => 'back_date', 'value' => '1', 'description' => 'Allow posting data for past days (0 for disabled, 1 for enabled)'],
                ['key' => 'default_user_role', 'value' => 'member', 'description' => 'Default role assigned to newly registered users'],
                ['key' => 'backup_schedule_hour', 'value' => '12:00 PM', 'description' => 'Hour of the day to perform backup (in format HH:MM AM/PM)',],
                ['key' => 'backup_frequency_days', 'value' => '7', 'description' => 'Create a backup every X days (0 to disable)'],
                ['key' => 'backup_retention_days', 'value' => '30', 'description' => 'Auto delete backups older than X days (set -1 to disable)'],
                ['key' => 'default_page_length', 'value' => '10', 'description' => 'Default page length for datatable'],
                ['key' => 'loan_limit_multiplier', 'value' => 3, 'description' => 'Multiplier for maximum loan amount based on member shares'],
                ['key' => 'enforce_loan_limit_multiplier', 'value' => 'on', 'description' => 'Whether to enforce the loan limit multiplier (on/off)'],
                ['key' => 'loan_topup_enabled', 'value' => 'off', 'description' => 'Enable or disable loan top-up feature for members (on/off)'],
                ['key' => 'loan_topup_max_percentage', 'value' => '50', 'description' => 'Maximum top-up amount as percentage of original loan (default: 50%)'],
                ['key' => 'loan_topup_min_amount', 'value' => '50000', 'description' => 'Minimum top-up amount in currency units (default: 50,000)'],
                ['key' => 'loan_topup_require_repayments', 'value' => 'on', 'description' => 'Require at least some repayments before allowing loan top-up (on/off)'],
                ['key' => 'money_point_low_float_threshold', 'value' => '50000', 'description' => 'Low float alert threshold in currency units. Alert triggers when float balance is below this amount (default: 5,000,000)'],
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
