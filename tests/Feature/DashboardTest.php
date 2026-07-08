<?php

namespace Tests\Feature;

use App\Models\AffiliationRequest;
use App\Models\Declaration;
use App\Models\Employer;
use App\Models\Employment;
use App\Models\Role;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_live_statistics_and_priority_alerts(): void
    {
        $role = Role::query()->create([
            'code' => 'ADMIN',
            'label' => 'Administrateur',
        ]);
        $admin = User::query()->create([
            'username' => 'dashboard_admin',
            'password_hash' => Hash::make('password123'),
            'full_name' => 'Dashboard Admin',
            'email' => 'dashboard@jemima.local',
            'is_active' => true,
        ]);
        $admin->roles()->attach($role->id);

        $employer = Employer::query()->create([
            'affiliation_number' => 'EMP-DASH-001',
            'legal_name' => 'Entreprise Tableau',
            'status' => 'ACTIVE',
            'verification_status' => 'VERIFIED',
        ]);
        $worker = Worker::query()->create([
            'social_security_number' => 'MAT-DASH-001',
            'first_name' => 'Jeanne',
            'last_name' => 'Ilunga',
            'status' => 'ACTIVE',
        ]);
        Employment::query()->create([
            'employer_id' => $employer->id,
            'worker_id' => $worker->id,
            'start_date' => now()->startOfYear()->toDateString(),
            'base_salary' => 1000,
            'is_declared_active' => true,
        ]);
        Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => now()->year,
            'period_month' => now()->month,
            'due_date' => now()->subDay()->toDateString(),
            'status' => 'DRAFT',
            'contribution_entry_mode' => 'GLOBAL',
            'total_declared_salary' => 1000,
            'total_declared_contribution' => 150,
            'global_contribution_amount' => 150,
            'global_amount_due' => 180,
        ]);
        AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-DASH-001',
            'status' => 'PENDING',
            'legal_name' => 'Nouvelle Entreprise',
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Vue d ensemble')
            ->assertSee('Entreprise Tableau')
            ->assertSee('Cotisation insuffisante')
            ->assertSee('30,00 CDF')
            ->assertSee('Affiliations en attente')
            ->assertSee('Evolution des cotisations')
            ->assertSee('Actions rapides');
    }
}
