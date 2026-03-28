<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $messageBody,
        public bool $consentGiven,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nieuw bericht via het Hermes Results contactformulier',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-form-submitted',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'messageBody' => $this->messageBody,
                'consentGiven' => $this->consentGiven,
            ],
        );
    }
}
