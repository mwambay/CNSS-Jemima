<?php

namespace Database\Seeders;

use App\Models\FraudRule;
use Illuminate\Database\Seeder;

class FraudRuleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rules = [
            [
                'code' => 'F001',
                'name' => 'Double declaration travailleur',
                'description' => 'Detecte un travailleur declare plusieurs fois sur la meme periode.',
                'severity' => 'HIGH',
                'is_active' => true,
            ],
            [
                'code' => 'F002',
                'name' => 'Variation salariale anormale',
                'description' => 'Detecte une variation de salaire incoherente par rapport a l historique recent.',
                'severity' => 'MEDIUM',
                'is_active' => true,
            ],
            [
                'code' => 'F003',
                'name' => 'Non versement declaration',
                'description' => 'Detecte les declarations sans paiement au dela du delai autorise.',
                'severity' => 'CRITICAL',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            FraudRule::query()->updateOrCreate(
                ['code' => $rule['code']],
                [
                    'name' => $rule['name'],
                    'description' => $rule['description'],
                    'severity' => $rule['severity'],
                    'is_active' => $rule['is_active'],
                ]
            );
        }
    }
}
