<?php

namespace Tests\Feature;

use App\Models\ContributionRate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContributionRateManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_contribution_rate_settings(): void
    {
        $admin = $this->createUserWithRole('ADMIN');

        $this->actingAs($admin)
            ->get('/parametres/cotisations')
            ->assertOk()
            ->assertSee('Parametres de cotisation');
    }

    public function test_agent_ses_cannot_manage_contribution_rates(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');

        $this->actingAs($agent)
            ->get('/parametres/cotisations')
            ->assertForbidden();
    }

    public function test_admin_can_create_and_update_contribution_rate(): void
    {
        $admin = $this->createUserWithRole('ADMIN');

        $this->actingAs($admin)->post('/parametres/cotisations', [
            'regime_code' => 'general',
            'effective_from' => '2026-01-01',
            'employer_rate' => 12,
            'worker_rate' => 4,
            'floor_amount' => 100,
            'ceiling_amount' => 5000,
            'is_active' => 1,
        ])->assertRedirect('/parametres/cotisations');

        $rate = ContributionRate::query()->firstOrFail();

        $this->assertDatabaseHas('contribution_rates', [
            'regime_code' => 'GENERAL',
            'employer_rate' => '12.0000',
            'worker_rate' => '4.0000',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)->put('/parametres/cotisations/'.$rate->id, [
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'employer_rate' => 13,
            'worker_rate' => 5,
            'floor_amount' => 100,
            'ceiling_amount' => 6000,
            'is_active' => 0,
        ])->assertRedirect('/parametres/cotisations');

        $this->assertDatabaseHas('contribution_rates', [
            'id' => $rate->id,
            'employer_rate' => '13.0000',
            'worker_rate' => '5.0000',
            'ceiling_amount' => '6000.00',
            'is_active' => 0,
        ]);
    }

    public function test_total_rate_cannot_exceed_one_hundred_percent(): void
    {
        $admin = $this->createUserWithRole('ADMIN');

        $this->actingAs($admin)->post('/parametres/cotisations', [
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'employer_rate' => 80,
            'worker_rate' => 30,
            'is_active' => 1,
        ])->assertSessionHasErrors('worker_rate');

        $this->assertDatabaseCount('contribution_rates', 0);
    }

    public function test_active_periods_cannot_overlap_for_the_same_regime(): void
    {
        $admin = $this->createUserWithRole('ADMIN');
        ContributionRate::query()->create([
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'effective_to' => '2026-12-31',
            'employer_rate' => 12,
            'worker_rate' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post('/parametres/cotisations', [
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-06-01',
            'employer_rate' => 13,
            'worker_rate' => 5,
            'is_active' => 1,
        ])->assertSessionHasErrors('effective_from');

        $this->assertDatabaseCount('contribution_rates', 1);
    }

    private function createUserWithRole(string $code): User
    {
        $role = Role::query()->create(['code' => $code, 'label' => $code]);
        $user = User::query()->create([
            'username' => strtolower($code),
            'password_hash' => Hash::make('password123'),
            'full_name' => $code.' User',
            'email' => strtolower($code).'@jemima.local',
            'is_active' => true,
        ]);
        $user->roles()->attach($role->id);

        return $user;
    }
}
