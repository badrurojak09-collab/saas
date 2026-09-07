<?php

namespace Database\Seeders\Tenant;

use App\Enums\Tenant\FeeBillingFrequency;
use App\Models\Tenant\FeeType;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $feeTypes = [
            [
                'code' => 'UKT-01',
                'name' => 'Uang Kuliah Tunggal (UKT)',
                'description' => 'Biaya pendidikan per semester',
                'default_amount' => 5000000.00,
                'billing_frequency' => FeeBillingFrequency::PerSemester,
            ],
            [
                'code' => 'REG-01',
                'name' => 'Biaya Registrasi Awal',
                'description' => 'Biaya registrasi mahasiswa baru',
                'default_amount' => 1500000.00,
                'billing_frequency' => FeeBillingFrequency::OneTime,
            ],
            [
                'code' => 'WISUDA-01',
                'name' => 'Biaya Wisuda',
                'description' => 'Biaya kelulusan dan toga wisuda',
                'default_amount' => 2000000.00,
                'billing_frequency' => FeeBillingFrequency::OneTime,
            ],
        ];

        foreach ($feeTypes as $fee) {
            FeeType::firstOrCreate(
                ['code' => $fee['code']],
                [
                    'name' => $fee['name'],
                    'description' => $fee['description'],
                    'default_amount' => $fee['default_amount'],
                    'billing_frequency' => $fee['billing_frequency'],
                    'status' => 'active',
                ]
            );
        }
    }
}
