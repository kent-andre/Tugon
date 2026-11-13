<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// The Summons (sent to the respondent) or the Notice of Hearing (sent to the
// complainant). Both carry the same case details; only the wording changes.
class KpNotice extends Mailable
{
    public function __construct(public string $kind, public array $case)
    {
    }

    public function envelope(): Envelope
    {
        $title = $this->kind === 'summons' ? 'Summons' : 'Notice of Hearing';

        return new Envelope(subject: "{$title}, Barangay Case No. {$this->case['ref']}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.kp-notice');
    }
}
