<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeleteAccountOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int    $otp,
        public string $name,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Account Deletion Request - Rice Mart',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.delete_account_otp',
            with: [
                'otp'  => $this->otp,
                'name' => $this->name,
            ],
        );
    }
}
