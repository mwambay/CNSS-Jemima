<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            FraudRuleSeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'password_hash' => Hash::make('admin1234'),
                'full_name' => 'System Admin',
                'email' => 'admin@jemima.local',
                'is_active' => true,
            ]
        );
    }
}
