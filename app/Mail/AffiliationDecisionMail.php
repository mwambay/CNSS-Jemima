<?php

namespace App\Mail;

use App\Models\AffiliationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AffiliationDecisionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly AffiliationRequest $affiliationRequest,
        public readonly string $decision
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->decision === 'APPROVED'
            ? 'Votre demande d affiliation CNSS est approuvee'
            : 'Decision sur votre demande d affiliation CNSS';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.affiliation-decision',
        );
    }
}
