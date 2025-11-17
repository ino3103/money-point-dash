<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FloatProvider;

class FloatProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            $providers = [
                [
                    'name' => 'mpesa',
                    'display_name' => 'M-Pesa',
                    'description' => 'Vodacom M-Pesa mobile money service',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'name' => 'tigopesa',
                    'display_name' => 'Tigo Pesa',
                    'description' => 'Tigo Pesa mobile money service',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'name' => 'airtemoney',
                    'display_name' => 'Airtel Money',
                    'description' => 'Airtel Money mobile money service',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
                [
                    'name' => 'crdb',
                    'display_name' => 'CRDB Bank',
                    'description' => 'CRDB Bank wallet/mobile banking',
                    'is_active' => true,
                    'sort_order' => 4,
                ],
                [
                    'name' => 'nmb',
                    'display_name' => 'NMB Bank',
                    'description' => 'NMB Bank wallet/mobile banking',
                    'is_active' => true,
                    'sort_order' => 5,
                ],
            ];

            $insertedCount = 0;
            $updatedCount = 0;
            $insertedProviders = [];
            $updatedProviders = [];

            foreach ($providers as $provider) {
                $existingProvider = FloatProvider::where('name', $provider['name'])->first();

                if (!$existingProvider) {
                    FloatProvider::create($provider);
                    $insertedCount++;
                    $insertedProviders[] = $provider['display_name'];
                } else {
                    // Update only if values have changed
                    $needsUpdate = false;
                    foreach ($provider as $key => $value) {
                        if ($existingProvider->$key != $value) {
                            $needsUpdate = true;
                            break;
                        }
                    }

                    if ($needsUpdate) {
                        $existingProvider->update($provider);
                        $updatedCount++;
                        $updatedProviders[] = $provider['display_name'];
                    }
                }
            }

            if ($insertedCount > 0) {
                $this->command->info("Inserted {$insertedCount} float providers:\n" . implode("\n", $insertedProviders));
            }

            if ($updatedCount > 0) {
                $this->command->info("Updated {$updatedCount} float providers:\n" . implode("\n", $updatedProviders));
            }

            if ($insertedCount == 0 && $updatedCount == 0) {
                $this->command->info("No new float providers were inserted or updated.");
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
