<?php

namespace Tests\Feature;

use App\Models\AffiliationRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AffiliationRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_submit_affiliation_request(): void
    {
        $response = $this->post('/affiliation', [
            'legal_name' => 'Kivu Services SARL',
            'phone' => '+243990000001',
            'email' => 'contact@kivu.test',
            'legal_form' => 'SARL',
            'rccm_number' => 'CD/KIN/RCCM/001',
            'primary_activity' => 'Transport',
            'street' => 'Avenue Kasavubu',
            'city' => 'Lubumbashi',
            'province' => 'Haut-Katanga',
        ]);

        $request = AffiliationRequest::query()->first();

        $response->assertRedirect(route('affiliation.submitted', $request));
        $this->assertDatabaseHas('affiliation_requests', [
            'legal_name' => 'Kivu Services SARL',
            'status' => 'PENDING',
        ]);
    }

    public function test_agent_ses_can_approve_affiliation_request_and_create_employer(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');
        $affiliationRequest = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-20260616-ABC123',
            'status' => 'PENDING',
            'legal_name' => 'Katanga Metal SARL',
            'phone' => '+243990000002',
            'email' => 'metal@katanga.test',
            'legal_form' => 'SARL',
            'rccm_number' => 'RCCM-002',
            'primary_activity' => 'Industrie',
            'street' => 'Route Kipushi',
            'district' => 'Golf',
            'municipality' => 'Lubumbashi',
            'city' => 'Lubumbashi',
            'province' => 'Haut-Katanga',
        ]);

        $response = $this
            ->actingAs($agent)
            ->post(route('affiliations.approve', $affiliationRequest), [
                'affiliation_number' => 'CNSS-0001',
            ]);

        $response->assertRedirect(route('affiliations.show', $affiliationRequest));

        $this->assertDatabaseHas('affiliation_requests', [
            'id' => $affiliationRequest->id,
            'status' => 'APPROVED',
            'processed_by_user_id' => $agent->id,
        ]);

        $this->assertDatabaseHas('employers', [
            'affiliation_number' => 'CNSS-0001',
            'legal_name' => 'Katanga Metal SARL',
            'registration_number' => 'RCCM-002',
            'sector' => 'Industrie',
            'verification_status' => 'VERIFIED',
            'address' => 'Route Kipushi, Golf, Lubumbashi, Lubumbashi, Haut-Katanga',
        ]);
    }

    public function test_affiliation_approval_accepts_long_primary_activity(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');
        $activity = str_repeat('Commerce international et prestations logistiques ', 3);
        $affiliationRequest = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-20260616-LONG01',
            'status' => 'PENDING',
            'legal_name' => 'Entreprise Activite Longue',
            'phone' => '+243990000004',
            'email' => 'longue@gmail.com',
            'legal_form' => 'AUTRE',
            'primary_activity' => $activity,
        ]);

        $response = $this
            ->actingAs($agent)
            ->post(route('affiliations.approve', $affiliationRequest), [
                'affiliation_number' => 'CNSS-LONG-001',
            ]);

        $response->assertRedirect(route('affiliations.show', $affiliationRequest));

        $this->assertDatabaseHas('employers', [
            'affiliation_number' => 'CNSS-LONG-001',
            'sector' => $activity,
        ]);
    }

    public function test_admin_or_agent_role_is_required_for_back_office(): void
    {
        $user = $this->createUser('viewer');

        $response = $this->actingAs($user)->get('/affiliations');

        $response->assertForbidden();
    }

    public function test_agent_ses_can_use_existing_business_api(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');

        $response = $this->actingAs($agent)->getJson('/api/employers');

        $response->assertOk();
    }

    public function test_agent_ses_can_reject_affiliation_request(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');
        $affiliationRequest = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-20260616-XYZ789',
            'status' => 'PENDING',
            'legal_name' => 'Incomplete Company',
            'phone' => '+243990000003',
        ]);

        $response = $this
            ->actingAs($agent)
            ->post(route('affiliations.reject', $affiliationRequest), [
                'rejection_reason' => 'Document incomplet.',
            ]);

        $response->assertRedirect(route('affiliations.show', $affiliationRequest));

        $this->assertDatabaseHas('affiliation_requests', [
            'id' => $affiliationRequest->id,
            'status' => 'REJECTED',
            'rejection_reason' => 'Document incomplet.',
        ]);
    }

    public function test_agent_must_wait_for_sdt_opinion_when_requested(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');
        $sdt = $this->createUserWithRole('SDT');
        $affiliationRequest = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-20260629-SDT001',
            'status' => 'PENDING',
            'legal_name' => 'Societe Consultative',
            'phone' => '+243990000005',
            'email' => 'consultative@test.local',
            'legal_form' => 'SARL',
            'primary_activity' => 'Services',
        ]);

        $this->actingAs($agent)
            ->post(route('affiliations.request-sdt-opinion', $affiliationRequest))
            ->assertRedirect(route('affiliations.show', $affiliationRequest));

        $this->assertDatabaseHas('affiliation_requests', [
            'id' => $affiliationRequest->id,
            'sdt_opinion_status' => 'REQUESTED',
            'sdt_opinion_requested_by_user_id' => $agent->id,
        ]);

        $this->actingAs($agent)
            ->post(route('affiliations.approve', $affiliationRequest), [
                'affiliation_number' => 'CNSS-SDT-001',
            ])
            ->assertSessionHasErrors('affiliation_number');

        $this->actingAs($sdt)
            ->post(route('sdt.affiliations.opinion', $affiliationRequest), [
                'sdt_opinion_status' => 'FAVORABLE',
                'sdt_opinion_note' => 'Les informations fournies sont coherentes avec le dossier presente.',
            ])
            ->assertRedirect(route('sdt.affiliations.show', $affiliationRequest));

        $this->assertDatabaseHas('affiliation_requests', [
            'id' => $affiliationRequest->id,
            'sdt_opinion_status' => 'FAVORABLE',
            'sdt_opinion_given_by_user_id' => $sdt->id,
        ]);

        $this->actingAs($agent)
            ->post(route('affiliations.approve', $affiliationRequest), [
                'affiliation_number' => 'CNSS-SDT-001',
            ])
            ->assertRedirect(route('affiliations.show', $affiliationRequest));

        $this->assertDatabaseHas('affiliation_requests', [
            'id' => $affiliationRequest->id,
            'status' => 'APPROVED',
        ]);
    }

    public function test_sdt_only_sees_affiliations_requested_for_opinion(): void
    {
        $agent = $this->createUserWithRole('AGENT_SES');
        $sdt = $this->createUserWithRole('SDT');
        $requested = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-20260629-SDT002',
            'status' => 'PENDING',
            'legal_name' => 'Demande Avec Avis',
            'sdt_opinion_status' => 'REQUESTED',
            'sdt_opinion_requested_at' => now(),
            'sdt_opinion_requested_by_user_id' => $agent->id,
        ]);
        AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-20260629-SDT003',
            'status' => 'PENDING',
            'legal_name' => 'Demande Sans Avis',
        ]);

        $this->actingAs($sdt)
            ->get(route('sdt.affiliations.index'))
            ->assertOk()
            ->assertSee('Demande Avec Avis')
            ->assertDontSee('Demande Sans Avis');

        $this->actingAs($sdt)
            ->get(route('sdt.affiliations.show', $requested))
            ->assertOk()
            ->assertSee('Transmettre l');
    }

    private function createUserWithRole(string $roleCode): User
    {
        $role = Role::query()->create([
            'code' => $roleCode,
            'label' => $roleCode,
        ]);

        $user = $this->createUser(strtolower($roleCode));
        $user->roles()->attach($role->id);

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
