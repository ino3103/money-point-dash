<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FloatProvider;

class FloatProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        foreach ($providers as $provider) {
            FloatProvider::firstOrCreate(
                ['name' => $provider['name']],
                $provider
            );
        }
    }
}
