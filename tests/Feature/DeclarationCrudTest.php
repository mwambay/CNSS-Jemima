<?php

namespace Tests\Feature;

use App\Models\ContributionRate;
use App\Models\Declaration;
use App\Models\Employer;
use App\Models\Employment;
use App\Models\Role;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeclarationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_declaration_api(): void
    {
        $response = $this->getJson('/api/declarations');

        $response->assertUnauthorized();
    }

    public function test_user_without_admin_role_gets_forbidden_on_declaration_api(): void
    {
        $user = $this->createUser('agent_declaration');

        $response = $this->actingAs($user)->getJson('/api/declarations');

        $response->assertForbidden();
    }

    public function test_can_create_declaration(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-D-001', 'Declaration Corp');

        $payload = [
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-04-30',
        ];

        $response = $this->actingAs($admin)->postJson('/api/declarations', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('employer_id', $employer->id)
            ->assertJsonPath('status', 'DRAFT')
            ->assertJsonPath('due_date', '2026-05-15');

        $this->assertDatabaseHas('declarations', [
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-05-15 00:00:00',
            'status' => 'DRAFT',
        ]);
    }

    public function test_can_upsert_line_on_draft_declaration_and_recalculate_totals(): void
    {
        $admin = $this->createAdminUser();
        $this->createDefaultRate();
        $employer = $this->createEmployer('EMP-D-002', 'Employer Two');
        $worker = $this->createWorkerForEmployer($employer, 'SS-D-001');

        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-04-30',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($admin)->postJson('/api/declarations/'.$declaration->id.'/lines', [
            'worker_id' => $worker->id,
            'gross_salary' => 1000,
            'contributable_salary' => 900,
            'worked_days' => 26,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('lines.0.worker_id', $worker->id)
            ->assertJsonPath('total_declared_salary', '1000.00')
            ->assertJsonPath('total_declared_contribution', '144.00');
    }

    public function test_cannot_add_line_for_worker_not_linked_to_employer(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-D-003', 'Employer Three');
        $otherEmployer = $this->createEmployer('EMP-D-004', 'Other Employer');

        $worker = $this->createWorkerForEmployer($otherEmployer, 'SS-D-002');

        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 5,
            'due_date' => '2026-05-31',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($admin)->postJson('/api/declarations/'.$declaration->id.'/lines', [
            'worker_id' => $worker->id,
            'gross_salary' => 800,
            'contributable_salary' => 700,
        ]);

        $response->assertStatus(422);
    }

    public function test_can_submit_and_validate_declaration(): void
    {
        $admin = $this->createAdminUser();
        $this->createDefaultRate();
        $employer = $this->createEmployer('EMP-D-005', 'Employer Five');
        $worker = $this->createWorkerForEmployer($employer, 'SS-D-003');

        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 6,
            'due_date' => '2026-06-30',
            'status' => 'DRAFT',
        ]);

        $this->actingAs($admin)->postJson('/api/declarations/'.$declaration->id.'/lines', [
            'worker_id' => $worker->id,
            'gross_salary' => 1200,
            'contributable_salary' => 1000,
        ])->assertOk();

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/submit')
            ->assertOk()
            ->assertJsonPath('status', 'SUBMITTED');

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/validate', [
                'validation_message' => 'Controle conforme',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'VALIDATED');

        $this->actingAs($admin)
            ->deleteJson('/api/declarations/'.$declaration->id)
            ->assertStatus(422);
    }

    public function test_upsert_line_applies_active_rate_and_persists_contribution_calc(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-D-006', 'Employer Six');
        $worker = $this->createWorkerForEmployer($employer, 'SS-D-006');

        $rate = ContributionRate::query()->create([
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'effective_to' => null,
            'employer_rate' => 12,
            'worker_rate' => 4,
            'ceiling_amount' => null,
            'floor_amount' => null,
            'is_active' => true,
        ]);

        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-04-30',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($admin)->postJson('/api/declarations/'.$declaration->id.'/lines', [
            'worker_id' => $worker->id,
            'gross_salary' => 1500,
            'contributable_salary' => 1000,
            'worked_days' => 24,
        ]);

        $lineId = (int) $response->json('lines.0.id');

        $response
            ->assertOk()
            ->assertJsonPath('total_declared_salary', '1500.00')
            ->assertJsonPath('total_declared_contribution', '160.00')
            ->assertJsonPath('lines.0.employer_amount', '120.00')
            ->assertJsonPath('lines.0.worker_amount', '40.00')
            ->assertJsonPath('lines.0.total_contribution', '160.00')
            ->assertJsonPath('lines.0.applied_rate.id', $rate->id);

        $this->assertDatabaseHas('contribution_calcs', [
            'declaration_line_id' => $lineId,
            'rate_id' => $rate->id,
            'employer_amount' => '120.00',
            'worker_amount' => '40.00',
            'total_amount' => '160.00',
        ]);
    }

    public function test_cannot_calculate_without_an_applicable_rate(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-D-008', 'Employer Eight');
        $worker = $this->createWorkerForEmployer($employer, 'SS-D-008');
        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 8,
            'due_date' => '2026-08-31',
            'status' => 'DRAFT',
        ]);

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/lines', [
                'worker_id' => $worker->id,
                'gross_salary' => 1000,
                'contributable_salary' => 900,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('contribution_rate');

        $this->assertDatabaseCount('declaration_lines', 0);
    }

    public function test_can_recalculate_declaration_with_newer_effective_rate(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-D-007', 'Employer Seven');
        $worker = $this->createWorkerForEmployer($employer, 'SS-D-007');

        ContributionRate::query()->create([
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'effective_to' => null,
            'employer_rate' => 10,
            'worker_rate' => 5,
            'ceiling_amount' => null,
            'floor_amount' => null,
            'is_active' => true,
        ]);

        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-04-30',
            'status' => 'DRAFT',
        ]);

        $this->actingAs($admin)->postJson('/api/declarations/'.$declaration->id.'/lines', [
            'worker_id' => $worker->id,
            'gross_salary' => 1400,
            'contributable_salary' => 1000,
            'worked_days' => 25,
        ])->assertOk()->assertJsonPath('total_declared_contribution', '150.00');

        $newRate = ContributionRate::query()->create([
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-03-01',
            'effective_to' => null,
            'employer_rate' => 12,
            'worker_rate' => 6,
            'ceiling_amount' => null,
            'floor_amount' => null,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/recalculate')
            ->assertOk()
            ->assertJsonPath('total_declared_contribution', '180.00')
            ->assertJsonPath('lines.0.total_contribution', '180.00')
            ->assertJsonPath('lines.0.applied_rate.id', $newRate->id);
    }

    public function test_can_record_and_submit_global_contribution_without_worker_lines(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-GLOBAL-001', 'Global Declaration Corp');
        $this->createWorkerForEmployer($employer, 'MAT-GLOBAL-001-A', 300000);
        $this->createWorkerForEmployer($employer, 'MAT-GLOBAL-001-B', 450000);
        ContributionRate::query()->create([
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'effective_to' => null,
            'employer_rate' => 12,
            'worker_rate' => 6,
            'is_active' => true,
        ]);
        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-05-15',
            'status' => 'DRAFT',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/declarations/'.$declaration->id.'/global-contribution-preview')
            ->assertOk()
            ->assertJsonPath('calculation.worker_count', 2)
            ->assertJsonPath('calculation.salary_envelope', 750000)
            ->assertJsonPath('calculation.total_rate', 18)
            ->assertJsonPath('calculation.amount_due', 135000)
            ->assertJsonPath('calculation.due_date', '2026-05-15')
            ->assertJsonPath('calculation.late_penalty_daily_rate', 0.5);

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/global-contribution', [
                'contributed_amount' => 120000,
                'contribution_date' => '2026-05-20',
            ])
            ->assertOk()
            ->assertJsonPath('contribution_entry_mode', 'GLOBAL')
            ->assertJsonPath('global_salary_envelope', '750000.00')
            ->assertJsonPath('global_employer_rate', '12.0000')
            ->assertJsonPath('global_worker_rate', '6.0000')
            ->assertJsonPath('global_worker_count', 2)
            ->assertJsonPath('global_amount_due', '135000.00')
            ->assertJsonPath('global_contribution_date', '2026-05-20')
            ->assertJsonPath('global_late_days', 5)
            ->assertJsonPath('global_late_penalty_rate', '0.5000')
            ->assertJsonPath('global_late_penalty_amount', '3375.00')
            ->assertJsonPath('global_total_payable', '138375.00')
            ->assertJsonPath('global_contribution_amount', '120000.00')
            ->assertJsonPath('global_contribution_difference', -18375)
            ->assertJsonPath('global_contribution_status', 'INSUFFISANT')
            ->assertJsonPath('total_declared_contribution', '120000.00')
            ->assertJsonCount(0, 'lines');

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/submit')
            ->assertOk()
            ->assertJsonPath('status', 'SUBMITTED');
    }

    public function test_global_contribution_mode_blocks_worker_lines_and_can_return_to_detailed_mode(): void
    {
        $admin = $this->createAdminUser();
        $this->createDefaultRate();
        $employer = $this->createEmployer('EMP-GLOBAL-002', 'Global Mode Corp');
        $worker = $this->createWorkerForEmployer($employer, 'MAT-GLOBAL-002', 1000);
        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-05-15',
            'status' => 'DRAFT',
        ]);

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/global-contribution', [
                'contributed_amount' => 1000,
                'contribution_date' => '2026-05-15',
            ])
            ->assertOk()
            ->assertJsonPath('global_late_days', 0)
            ->assertJsonPath('global_late_penalty_amount', '0.00')
            ->assertJsonPath('global_total_payable', '160.00');

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/lines', [
                'worker_id' => $worker->id,
                'gross_salary' => 1000,
                'contributable_salary' => 900,
            ])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/use-detailed-entry')
            ->assertOk()
            ->assertJsonPath('contribution_entry_mode', 'DETAILED')
            ->assertJsonPath('global_contribution_amount', null)
            ->assertJsonPath('global_amount_due', null)
            ->assertJsonPath('global_contribution_date', null)
            ->assertJsonPath('global_late_penalty_amount', null)
            ->assertJsonPath('global_total_payable', null)
            ->assertJsonPath('total_declared_contribution', '0.00');
    }

    public function test_global_contribution_is_blocked_when_an_active_worker_has_no_salary(): void
    {
        $admin = $this->createAdminUser();
        $this->createDefaultRate();
        $employer = $this->createEmployer('EMP-GLOBAL-003', 'Incomplete Salaries Corp');
        $this->createWorkerForEmployer($employer, 'MAT-GLOBAL-003');
        $declaration = Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => 2026,
            'period_month' => 4,
            'due_date' => '2026-05-15',
            'status' => 'DRAFT',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/declarations/'.$declaration->id.'/global-contribution-preview')
            ->assertStatus(422)
            ->assertJsonValidationErrors('base_salary');

        $this->actingAs($admin)
            ->postJson('/api/declarations/'.$declaration->id.'/global-contribution', [
                'contributed_amount' => 1000,
                'contribution_date' => '2026-05-15',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('base_salary');
    }

    private function createAdminUser(): User
    {
        $adminRole = Role::query()->create([
            'code' => 'ADMIN',
            'label' => 'Administrateur',
        ]);

        $user = $this->createUser('admin_declaration');
        $user->roles()->attach($adminRole->id);

        return $user;
    }

    private function createDefaultRate(): ContributionRate
    {
        return ContributionRate::query()->create([
            'regime_code' => 'GENERAL',
            'effective_from' => '2026-01-01',
            'effective_to' => null,
            'employer_rate' => 12,
            'worker_rate' => 4,
            'ceiling_amount' => null,
            'floor_amount' => null,
            'is_active' => true,
        ]);
    }

    private function createUser(string $username): User
    {
        return User::query()->create([
            'username' => $username,
            'password_hash' => Hash::make('password123'),
            'full_name' => ucfirst($username).' User',
            'email' => $username.'@jemima.local',
            'is_active' => true,
        ]);
    }

    private function createEmployer(string $affiliationNumber, string $legalName): Employer
    {
        return Employer::query()->create([
            'affiliation_number' => $affiliationNumber,
            'legal_name' => $legalName,
            'status' => 'ACTIVE',
            'verification_status' => 'VERIFIED',
        ]);
    }

    private function createWorkerForEmployer(Employer $employer, string $ssn, ?float $baseSalary = null): Worker
    {
        $worker = Worker::query()->create([
            'social_security_number' => $ssn,
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'status' => 'ACTIVE',
        ]);

        Employment::query()->create([
            'employer_id' => $employer->id,
            'worker_id' => $worker->id,
            'start_date' => '2026-01-01',
            'base_salary' => $baseSalary,
            'is_declared_active' => true,
        ]);

        return $worker;
    }
}
