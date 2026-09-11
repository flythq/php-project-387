<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly bool $forHost = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Запись на звонок создана',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.booking-created',
        );
    }
}
