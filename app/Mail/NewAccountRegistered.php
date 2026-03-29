<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAccountRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nieuw account Hermes Results',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-account-registered',
            with: [
                'name' => $this->name,
                'email' => $this->email,
            ],
        );
    }
}
