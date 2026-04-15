<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\Employment;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployerDetailsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_employer_details_with_workers(): void
    {
        $user = User::query()->create([
            'username' => 'viewer',
            'password_hash' => Hash::make('password123'),
            'full_name' => 'Viewer User',
            'email' => 'viewer@jemima.local',
            'is_active' => true,
        ]);

        $employer = Employer::query()->create([
            'affiliation_number' => 'EMP-DETAIL-001',
            'legal_name' => 'Detail Corp',
            'tax_id' => 'NIF-001',
            'status' => 'ACTIVE',
            'verification_status' => 'VERIFIED',
        ]);

        $worker = Worker::query()->create([
            'social_security_number' => 'SS-DETAIL-001',
            'national_id' => 'CIN-DETAIL-001',
            'first_name' => 'Awa',
            'last_name' => 'Sow',
            'status' => 'ACTIVE',
        ]);

        Employment::query()->create([
            'employer_id' => $employer->id,
            'worker_id' => $worker->id,
            'contract_type' => 'CDI',
            'start_date' => '2026-01-15',
            'base_salary' => 150000,
            'is_declared_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/employeurs/'.$employer->id);

        $response
            ->assertOk()
            ->assertSee('Detail Corp')
            ->assertSee('SS-DETAIL-001')
            ->assertSee('Awa Sow')
            ->assertSee('CDI');
    }

    public function test_guest_is_redirected_when_opening_employer_details_page(): void
    {
        $employer = Employer::query()->create([
            'affiliation_number' => 'EMP-DETAIL-002',
            'legal_name' => 'Guest Corp',
        ]);

        $response = $this->get('/employeurs/'.$employer->id);

        $response->assertRedirect('/login');
    }
}
