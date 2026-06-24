<?php

namespace App\Services;

use App\Mail\AffiliationDecisionMail;
use App\Mail\ContributionReminderMail;
use App\Models\AffiliationRequest;
use App\Models\Declaration;
use App\Models\MailDispatch;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailDispatchService
{
    public function sendAffiliationDecision(AffiliationRequest $affiliationRequest): bool
    {
        if (! $affiliationRequest->email || ! in_array($affiliationRequest->status, ['APPROVED', 'REJECTED'], true)) {
            return false;
        }

        $type = $affiliationRequest->status === 'APPROVED'
            ? MailDispatch::AFFILIATION_APPROVED
            : MailDispatch::AFFILIATION_REJECTED;

        if ($this->alreadySent($type, $affiliationRequest)) {
            return false;
        }

        $affiliationRequest->loadMissing('employer');

        try {
            Mail::to($affiliationRequest->email)->send(
                new AffiliationDecisionMail($affiliationRequest, $affiliationRequest->status)
            );
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        $this->record($type, $affiliationRequest, $affiliationRequest->email, $this->recipientName($affiliationRequest), [
            'tracking_number' => $affiliationRequest->tracking_number,
            'status' => $affiliationRequest->status,
        ]);

        return true;
    }

    public function sendContributionReminders(CarbonInterface $today): array
    {
        $targets = [
            MailDispatch::CONTRIBUTION_REMINDER_D_MINUS_5 => $today->copy()->addDays(5)->toDateString(),
            MailDispatch::CONTRIBUTION_REMINDER_D_DAY => $today->toDateString(),
        ];

        $sent = 0;
        $skipped = 0;

        foreach ($targets as $type => $dueDate) {
            Declaration::query()
                ->with('employer')
                ->whereDate('due_date', $dueDate)
                ->whereIn('status', ['DRAFT', 'SUBMITTED'])
                ->whereNull('global_contribution_amount')
                ->orderBy('id')
                ->chunkById(100, function ($declarations) use ($type, &$sent, &$skipped): void {
                    foreach ($declarations as $declaration) {
                        if ($this->sendContributionReminder($declaration, $type)) {
                            $sent++;
                            continue;
                        }

                        $skipped++;
                    }
                });
        }

        return ['sent' => $sent, 'skipped' => $skipped];
    }

    public function sendContributionReminder(Declaration $declaration, string $type): bool
    {
        $declaration->loadMissing('employer');

        $email = $declaration->employer?->email;

        if (! $email || $declaration->global_contribution_amount !== null || $this->alreadySent($type, $declaration)) {
            return false;
        }

        $reminderType = $type === MailDispatch::CONTRIBUTION_REMINDER_D_DAY ? 'D_DAY' : 'D_MINUS_5';

        try {
            Mail::to($email)->send(new ContributionReminderMail($declaration, $reminderType));
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        $this->record($type, $declaration, $email, $declaration->employer?->legal_name, [
            'declaration_id' => $declaration->id,
            'period_month' => $declaration->period_month,
            'period_year' => $declaration->period_year,
            'due_date' => $declaration->due_date?->format('Y-m-d'),
        ]);

        return true;
    }

    private function alreadySent(string $type, Model $emailable): bool
    {
        return MailDispatch::query()
            ->where('type', $type)
            ->where('emailable_type', $emailable::class)
            ->where('emailable_id', $emailable->getKey())
            ->exists();
    }

    private function record(string $type, Model $emailable, string $email, ?string $name, array $metadata): void
    {
        MailDispatch::query()->create([
            'type' => $type,
            'recipient_email' => $email,
            'recipient_name' => $name,
            'emailable_type' => $emailable::class,
            'emailable_id' => $emailable->getKey(),
            'sent_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    private function recipientName(AffiliationRequest $affiliationRequest): ?string
    {
        return $affiliationRequest->legal_name ?: $affiliationRequest->physical_employer_name;
    }
}
