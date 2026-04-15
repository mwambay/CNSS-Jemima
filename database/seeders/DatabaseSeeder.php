<?php

namespace Database\Seeders;

use App\Models\Role;
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

        $admin = User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'password_hash' => Hash::make('admin1234'),
                'full_name' => 'System Admin',
                'email' => 'admin@jemima.local',
                'is_active' => true,
            ]
        );

        $adminRole = Role::query()->where('code', 'ADMIN')->first();
        if ($adminRole !== null) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
