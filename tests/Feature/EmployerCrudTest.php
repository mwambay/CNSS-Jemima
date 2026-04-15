<?php

namespace Tests\Feature;

use App\Models\Employer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_employers(): void
    {
        Employer::query()->create([
            'affiliation_number' => 'AFF-001',
            'legal_name' => 'ACME SARL',
        ]);

        $response = $this->getJson('/api/employers');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.affiliation_number', 'AFF-001');
    }

    public function test_can_create_employer_with_valid_payload(): void
    {
        $payload = [
            'affiliation_number' => 'AFF-002',
            'legal_name' => 'Global Industries',
            'tax_id' => 'TAX-002',
            'status' => 'ACTIVE',
            'verification_status' => 'PENDING',
            'email' => 'contact@global.test',
        ];

        $response = $this->postJson('/api/employers', $payload);

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
        $response = $this->postJson('/api/employers', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['affiliation_number', 'legal_name']);
    }

    public function test_can_update_an_employer(): void
    {
        $employer = Employer::query()->create([
            'affiliation_number' => 'AFF-003',
            'legal_name' => 'Old Name',
        ]);

        $response = $this->putJson('/api/employers/'.$employer->id, [
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
        $employer = Employer::query()->create([
            'affiliation_number' => 'AFF-004',
            'legal_name' => 'Delete Me',
        ]);

        $response = $this->deleteJson('/api/employers/'.$employer->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('employers', [
            'id' => $employer->id,
        ]);
    }
}
