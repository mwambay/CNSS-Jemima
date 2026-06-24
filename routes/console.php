<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cnss:send-contribution-reminders', function (): int {
    $result = app(\App\Services\MailDispatchService::class)->sendContributionReminders(today());

    $this->info("Rappels cotisation envoyes: {$result['sent']}. Ignores: {$result['skipped']}.");

    return 0;
})->purpose('Envoyer les rappels de cotisation CNSS a J-5 et le jour de l echeance');

Artisan::command('cnss:test-mail {email}', function (string $email): int {
    \Illuminate\Support\Facades\Mail::raw(
        'Ceci est un mail de test envoye depuis Jemima CNSS.',
        fn ($message) => $message->to($email)->subject('Test email CNSS')
    );

    $this->info("Mail de test envoye vers {$email}.");

    return 0;
})->purpose('Envoyer un mail de test CNSS vers une adresse donnee');

Schedule::command('cnss:send-contribution-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping();
