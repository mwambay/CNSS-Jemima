<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            ['code' => 'ADMIN', 'label' => 'Administrateur'],
            ['code' => 'AGENT_SES', 'label' => 'Agent SES'],
            ['code' => 'SDT', 'label' => 'Service de traitement'],
            ['code' => 'CONTROLE', 'label' => 'Controle'],
            ['code' => 'RECOUVREMENT', 'label' => 'Recouvrement'],
            ['code' => 'AUDIT', 'label' => 'Audit'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['code' => $role['code']],
                ['label' => $role['label']]
            );
        }
    }
}
