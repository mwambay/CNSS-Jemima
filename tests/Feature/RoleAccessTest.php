<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_sdt_only_sees_dedicated_dashboard_and_no_business_navigation(): void
    {
        $sdt = $this->userWithRole('SDT');

        $this->actingAs($sdt)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tableau de bord SDT')
            ->assertSee('Rapport cotisation')
            ->assertSee('Rapport activite')
            ->assertDontSee('href="http://localhost/affiliations"', false)
            ->assertDontSee('href="http://localhost/employeurs"', false)
            ->assertDontSee('href="http://localhost/travailleurs"', false)
            ->assertDontSee('href="http://localhost/declarations"', false)
            ->assertDontSee('href="http://localhost/utilisateurs"', false);
    }

    public function test_sdt_cannot_access_business_pages(): void
    {
        $sdt = $this->userWithRole('SDT');

        $this->actingAs($sdt)->get('/declarations')->assertForbidden();
        $this->actingAs($sdt)->get('/employeurs')->assertForbidden();
        $this->actingAs($sdt)->get('/travailleurs')->assertForbidden();
        $this->actingAs($sdt)->get('/affiliations')->assertForbidden();
        $this->actingAs($sdt)->get('/utilisateurs')->assertForbidden();
    }

    public function test_sdt_can_open_reports_pages(): void
    {
        $sdt = $this->userWithRole('SDT');

        $this->actingAs($sdt)
            ->get('/sdt/rapports/cotisations')
            ->assertOk()
            ->assertSee('Rapport de cotisation')
            ->assertSee('Montant du');

        $this->actingAs($sdt)
            ->get('/sdt/rapports/activite')
            ->assertOk()
            ->assertSee('Rapport d activité')
            ->assertSee('Affiliations reçues');
    }

    public function test_agent_ses_cannot_open_sdt_reports(): void
    {
        $agent = $this->userWithRole('AGENT_SES');

        $this->actingAs($agent)->get('/sdt/rapports/cotisations')->assertForbidden();
        $this->actingAs($agent)->get('/sdt/rapports/activite')->assertForbidden();
    }

    public function test_agent_ses_can_open_declarations_as_cotisation_page(): void
    {
        $agent = $this->userWithRole('AGENT_SES');

        $this->actingAs($agent)
            ->get('/declarations')
            ->assertOk()
            ->assertSee('Déclarations')
            ->assertDontSee('Lecture seule');
    }

    private function userWithRole(string $roleCode): User
    {
        $role = Role::query()->create([
            'code' => $roleCode,
            'label' => $roleCode,
        ]);

        $user = User::query()->create([
            'username' => strtolower($roleCode),
            'password_hash' => Hash::make('password123'),
            'full_name' => $roleCode.' User',
            'email' => strtolower($roleCode).'@jemima.local',
            'is_active' => true,
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }
}
