<?php

namespace App\Mail;

use App\Models\Declaration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContributionReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Declaration $declaration,
        public readonly string $reminderType
    ) {
    }

    public function envelope(): Envelope
    {
        $period = str_pad((string) $this->declaration->period_month, 2, '0', STR_PAD_LEFT)
            .'/'.$this->declaration->period_year;

        $subject = $this->reminderType === 'D_DAY'
            ? "Rappel CNSS: cotisation attendue aujourd'hui pour {$period}"
            : "Rappel CNSS: cotisation a verser dans 5 jours pour {$period}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contribution-reminder',
        );
    }
}
