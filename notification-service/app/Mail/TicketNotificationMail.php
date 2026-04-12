<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $type,
        public readonly string $message,
        public readonly string $referenceId
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Notification: " . ucfirst($this->type),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-notification',
            with: [
                'type' => $this->type,
                'messageText' => $this->message,
                'referenceId' => $this->referenceId,
            ],
        );
    }
}
