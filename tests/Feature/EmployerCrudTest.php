<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_employer_api(): void
    {
        $response = $this->getJson('/api/employers');

        $response->assertUnauthorized();
    }

    public function test_user_without_admin_role_gets_forbidden_on_employer_api(): void
    {
        $user = $this->createUser('agent');

        $response = $this->actingAs($user)->getJson('/api/employers');

        $response->assertForbidden();
    }

    public function test_can_list_employers(): void
    {
        $admin = $this->createAdminUser();

        Employer::query()->create([
            'affiliation_number' => 'AFF-001',
            'legal_name' => 'ACME SARL',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/employers');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.affiliation_number', 'AFF-001');
    }

    public function test_can_create_employer_with_valid_payload(): void
    {
        $admin = $this->createAdminUser();

        $payload = [
            'affiliation_number' => 'AFF-002',
            'legal_name' => 'Global Industries',
            'tax_id' => 'TAX-002',
            'status' => 'ACTIVE',
            'verification_status' => 'PENDING',
            'email' => 'contact@global.test',
        ];

        $response = $this->actingAs($admin)->postJson('/api/employers', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('affiliation_number', 'AFF-002')
            ->assertJsonPath('legal_name', 'Global Industries');

        $this->assertDatabaseHas('employers', [
            'affiliation_number' => 'AFF-002',
            'legal_name' => 'Global Industries',
        ]);
    }

    public function test_create_employer_validates_required_fields(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->postJson('/api/employers', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['affiliation_number', 'legal_name']);
    }

    public function test_can_update_an_employer(): void
    {
        $admin = $this->createAdminUser();

        $employer = Employer::query()->create([
            'affiliation_number' => 'AFF-003',
            'legal_name' => 'Old Name',
        ]);

        $response = $this->actingAs($admin)->putJson('/api/employers/'.$employer->id, [
            'affiliation_number' => 'AFF-003',
            'legal_name' => 'New Name',
            'status' => 'SUSPENDED',
            'verification_status' => 'VERIFIED',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('legal_name', 'New Name')
            ->assertJsonPath('status', 'SUSPENDED');

        $this->assertDatabaseHas('employers', [
            'id' => $employer->id,
            'legal_name' => 'New Name',
            'status' => 'SUSPENDED',
        ]);
    }

    public function test_can_delete_an_employer(): void
    {
        $admin = $this->createAdminUser();

        $employer = Employer::query()->create([
            'affiliation_number' => 'AFF-004',
            'legal_name' => 'Delete Me',
        ]);

        $response = $this->actingAs($admin)->deleteJson('/api/employers/'.$employer->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('employers', [
            'id' => $employer->id,
        ]);
    }

    private function createAdminUser(): User
    {
        $adminRole = Role::query()->create([
            'code' => 'ADMIN',
            'label' => 'Administrateur',
        ]);

        $user = $this->createUser('admin');
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
}
