<?php

namespace Database\Seeders;

use App\Models\FloatType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FloatTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floatTypes = [
            [
                'name' => 'M-Pesa',
                'code' => 'MPESA',
                'description' => 'Vodacom M-Pesa mobile money',
                'is_active' => true,
            ],
            [
                'name' => 'Tigo Pesa',
                'code' => 'TIGO',
                'description' => 'Tigo Pesa mobile money',
                'is_active' => true,
            ],
            [
                'name' => 'Airtel Money',
                'code' => 'AIRTEL',
                'description' => 'Airtel Money mobile money',
                'is_active' => true,
            ],
            [
                'name' => 'Halopesa',
                'code' => 'HALO',
                'description' => 'Halotel Halopesa mobile money',
                'is_active' => true,
            ],
            [
                'name' => 'CRDB',
                'code' => 'CRDB',
                'description' => 'CRDB Bank account',
                'is_active' => true,
            ],
            [
                'name' => 'NMB',
                'code' => 'NMB',
                'description' => 'NMB Bank account',
                'is_active' => true,
            ],
            [
                'name' => 'NBC',
                'code' => 'NBC',
                'description' => 'NBC Bank account',
                'is_active' => true,
            ],
            [
                'name' => 'Exim Bank',
                'code' => 'EXIM',
                'description' => 'Exim Bank account',
                'is_active' => true,
            ],
            [
                'name' => 'Stanbic Bank',
                'code' => 'STANBIC',
                'description' => 'Stanbic Bank account',
                'is_active' => true,
            ],
            [
                'name' => 'Equity Bank',
                'code' => 'EQUITY',
                'description' => 'Equity Bank account',
                'is_active' => true,
            ],
        ];

        foreach ($floatTypes as $floatType) {
            FloatType::updateOrCreate(
                ['code' => $floatType['code']],
                $floatType
            );
        }
    }
}
