<?php

namespace App\Mail;

use App\Models\AffiliationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AffiliationTrackingMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly AffiliationRequest $affiliationRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Numéro de suivi de votre demande d'affiliation CNSS");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.affiliation-tracking');
    }
}
