<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\Employment;
use App\Models\Role;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_worker_api(): void
    {
        $response = $this->getJson('/api/workers');

        $response->assertUnauthorized();
    }

    public function test_user_without_admin_role_gets_forbidden_on_worker_api(): void
    {
        $user = $this->createUser('agent_worker');

        $response = $this->actingAs($user)->getJson('/api/workers');

        $response->assertForbidden();
    }

    public function test_can_list_workers(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-001', 'ACME');

        $worker = Worker::query()->create([
            'social_security_number' => 'SS-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'status' => 'ACTIVE',
        ]);

        Employment::query()->create([
            'employer_id' => $employer->id,
            'worker_id' => $worker->id,
            'start_date' => '2026-01-01',
            'is_declared_active' => true,
        ]);

        $response = $this->actingAs($admin)->getJson('/api/workers');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.social_security_number', 'SS-001')
            ->assertJsonPath('0.employer_name', 'ACME');
    }

    public function test_can_create_worker_with_valid_payload(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-002', 'Global Industries');

        $payload = [
            'social_security_number' => 'SS-002',
            'national_id' => 'CIN-002',
            'first_name' => 'Ali',
            'last_name' => 'Moussa',
            'birth_date' => '1990-03-20',
            'gender' => 'M',
            'status' => 'ACTIVE',
            'employer_id' => $employer->id,
            'employment_start_date' => '2026-02-01',
            'contract_type' => 'CDI',
            'base_salary' => 320000,
        ];

        $response = $this->actingAs($admin)->postJson('/api/workers', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('social_security_number', 'SS-002')
            ->assertJsonPath('employer_id', $employer->id);

        $this->assertDatabaseHas('workers', [
            'social_security_number' => 'SS-002',
            'first_name' => 'Ali',
            'last_name' => 'Moussa',
        ]);

        $this->assertDatabaseHas('employments', [
            'employer_id' => $employer->id,
            'contract_type' => 'CDI',
        ]);

        $employment = Employment::query()->where('worker_id', $response->json('id'))->first();
        $this->assertNotNull($employment);
        $this->assertSame('2026-02-01', substr((string) $employment->start_date, 0, 10));
    }

    public function test_create_worker_validates_required_fields(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->postJson('/api/workers', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'social_security_number',
                'first_name',
                'last_name',
                'employer_id',
                'employment_start_date',
            ]);
    }

    public function test_can_update_a_worker_and_active_employment(): void
    {
        $admin = $this->createAdminUser();
        $oldEmployer = $this->createEmployer('EMP-003', 'Old Corp');
        $newEmployer = $this->createEmployer('EMP-004', 'New Corp');

        $worker = Worker::query()->create([
            'social_security_number' => 'SS-003',
            'first_name' => 'Old',
            'last_name' => 'Name',
            'status' => 'ACTIVE',
        ]);

        $employment = Employment::query()->create([
            'employer_id' => $oldEmployer->id,
            'worker_id' => $worker->id,
            'start_date' => '2025-01-01',
            'is_declared_active' => true,
        ]);

        $response = $this->actingAs($admin)->putJson('/api/workers/'.$worker->id, [
            'social_security_number' => 'SS-003',
            'first_name' => 'New',
            'last_name' => 'Name',
            'status' => 'SUSPENDED',
            'employer_id' => $newEmployer->id,
            'employment_start_date' => '2026-03-01',
            'contract_type' => 'CDD',
            'base_salary' => 210000,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('first_name', 'New')
            ->assertJsonPath('employer_id', $newEmployer->id)
            ->assertJsonPath('status', 'SUSPENDED');

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'first_name' => 'New',
            'status' => 'SUSPENDED',
        ]);

        $this->assertDatabaseHas('employments', [
            'id' => $employment->id,
            'worker_id' => $worker->id,
            'employer_id' => $newEmployer->id,
            'contract_type' => 'CDD',
        ]);

        $employment->refresh();
        $this->assertSame('2026-03-01', substr((string) $employment->start_date, 0, 10));
    }

    public function test_can_delete_a_worker(): void
    {
        $admin = $this->createAdminUser();
        $employer = $this->createEmployer('EMP-005', 'Delete Corp');

        $worker = Worker::query()->create([
            'social_security_number' => 'SS-004',
            'first_name' => 'Delete',
            'last_name' => 'Me',
            'status' => 'ACTIVE',
        ]);

        $employment = Employment::query()->create([
            'employer_id' => $employer->id,
            'worker_id' => $worker->id,
            'start_date' => '2026-01-10',
            'is_declared_active' => true,
        ]);

        $response = $this->actingAs($admin)->deleteJson('/api/workers/'.$worker->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('workers', [
            'id' => $worker->id,
        ]);

        $this->assertDatabaseMissing('employments', [
            'id' => $employment->id,
        ]);
    }

    private function createAdminUser(): User
    {
        $adminRole = Role::query()->create([
            'code' => 'ADMIN',
            'label' => 'Administrateur',
        ]);

        $user = $this->createUser('admin_worker');
        $user->roles()->attach($adminRole->id);

        return $user;
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
}
