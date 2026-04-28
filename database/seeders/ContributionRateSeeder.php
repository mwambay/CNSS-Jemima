<?php

namespace Database\Seeders;

use App\Models\ContributionRate;
use Illuminate\Database\Seeder;

class ContributionRateSeeder extends Seeder
{
    public function run(): void
    {
        ContributionRate::query()->updateOrCreate(
            [
                'regime_code' => 'GENERAL',
                'effective_from' => '2026-01-01',
            ],
            [
                'effective_to' => null,
                'employer_rate' => 12.00,
                'worker_rate' => 4.00,
                'ceiling_amount' => null,
                'floor_amount' => null,
                'is_active' => true,
            ]
        );
    }
}
