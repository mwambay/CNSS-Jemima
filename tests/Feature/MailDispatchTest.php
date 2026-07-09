<?php

namespace Tests\Feature;

use App\Mail\AffiliationDecisionMail;
use App\Mail\AffiliationTrackingMail;
use App\Mail\ContributionReminderMail;
use App\Models\AffiliationRequest;
use App\Models\Declaration;
use App\Models\Employer;
use App\Models\MailDispatch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_affiliation_submission_sends_tracking_number_email(): void
    {
        Mail::fake();

        $this->post('/affiliation', [
            'legal_name' => 'Entreprise Suivi',
            'phone' => '+243990000010',
            'email' => 'suivi@gmail.com',
            'legal_form' => 'SARL',
            'primary_activity' => 'Commerce',
        ])->assertRedirect();

        $request = AffiliationRequest::query()->where('email', 'suivi@gmail.com')->firstOrFail();

        Mail::assertSent(AffiliationTrackingMail::class, function (AffiliationTrackingMail $mail) use ($request): bool {
            return $mail->affiliationRequest->is($request)
                && $mail->hasTo('suivi@gmail.com');
        });

        $this->assertDatabaseHas('mail_dispatches', [
            'type' => MailDispatch::AFFILIATION_TRACKING,
            'recipient_email' => 'suivi@gmail.com',
        ]);
    }

    public function test_affiliation_approval_sends_email_and_records_dispatch(): void
    {
        Mail::fake();

        $admin = $this->adminUser();
        $request = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-MAIL-001',
            'status' => 'PENDING',
            'legal_name' => 'Entreprise Mail',
            'email' => 'employeur@gmail.com',
        ]);

        $this->actingAs($admin)
            ->post(route('affiliations.approve', $request), [
                'affiliation_number' => 'CNSS-MAIL-001',
            ])
            ->assertRedirect(route('affiliations.show', $request));

        Mail::assertSent(AffiliationDecisionMail::class, function (AffiliationDecisionMail $mail): bool {
            return $mail->decision === 'APPROVED'
                && $mail->hasTo('employeur@gmail.com');
        });

        $this->assertDatabaseHas('mail_dispatches', [
            'type' => MailDispatch::AFFILIATION_APPROVED,
            'recipient_email' => 'employeur@gmail.com',
        ]);
    }

    public function test_affiliation_rejection_sends_email_with_reason(): void
    {
        Mail::fake();

        $admin = $this->adminUser();
        $request = AffiliationRequest::query()->create([
            'tracking_number' => 'AFF-MAIL-002',
            'status' => 'PENDING',
            'legal_name' => 'Entreprise Rejet',
            'email' => 'rejet@gmail.com',
        ]);

        $this->actingAs($admin)
            ->post(route('affiliations.reject', $request), [
                'rejection_reason' => 'Pieces incompletes.',
            ])
            ->assertRedirect(route('affiliations.show', $request));

        Mail::assertSent(AffiliationDecisionMail::class, function (AffiliationDecisionMail $mail): bool {
            return $mail->decision === 'REJECTED'
                && $mail->hasTo('rejet@gmail.com');
        });

        $this->assertDatabaseHas('mail_dispatches', [
            'type' => MailDispatch::AFFILIATION_REJECTED,
            'recipient_email' => 'rejet@gmail.com',
        ]);
    }

    public function test_contribution_reminder_command_sends_due_emails_once(): void
    {
        Mail::fake();

        $employer = Employer::query()->create([
            'affiliation_number' => 'CNSS-REM-001',
            'legal_name' => 'Entreprise Rappel',
            'status' => 'ACTIVE',
            'verification_status' => 'VERIFIED',
            'email' => 'rappel@gmail.com',
        ]);
        Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => now()->year,
            'period_month' => now()->month,
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'DRAFT',
            'total_declared_salary' => 100000,
            'total_declared_contribution' => 18000,
        ]);

        $this->artisan('cnss:send-contribution-reminders')->assertSuccessful();
        $this->artisan('cnss:send-contribution-reminders')->assertSuccessful();

        Mail::assertSent(ContributionReminderMail::class, 1);
        $this->assertDatabaseCount('mail_dispatches', 1);
        $this->assertDatabaseHas('mail_dispatches', [
            'type' => MailDispatch::CONTRIBUTION_REMINDER_D_MINUS_5,
            'recipient_email' => 'rappel@gmail.com',
        ]);
    }

    public function test_contribution_reminder_is_not_sent_when_contribution_is_recorded(): void
    {
        Mail::fake();

        $employer = Employer::query()->create([
            'affiliation_number' => 'CNSS-REM-002',
            'legal_name' => 'Entreprise Conforme',
            'status' => 'ACTIVE',
            'verification_status' => 'VERIFIED',
            'email' => 'conforme@gmail.com',
        ]);
        Declaration::query()->create([
            'employer_id' => $employer->id,
            'period_year' => now()->year,
            'period_month' => now()->month,
            'due_date' => now()->toDateString(),
            'status' => 'DRAFT',
            'global_contribution_amount' => 18000,
            'total_declared_salary' => 100000,
            'total_declared_contribution' => 18000,
        ]);

        $this->artisan('cnss:send-contribution-reminders')->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertDatabaseCount('mail_dispatches', 0);
    }

    private function adminUser(): User
    {
        $role = Role::query()->create([
            'code' => 'ADMIN',
            'label' => 'Administrateur',
        ]);

        $user = User::query()->create([
            'username' => 'mail_admin',
            'password_hash' => Hash::make('password123'),
            'full_name' => 'Mail Admin',
            'email' => 'admin@jemima.local',
            'is_active' => true,
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }
}
