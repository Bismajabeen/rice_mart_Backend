<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeleteShopOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int    $otp,
        public string $name,
        public string $shopName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Confirm Shop Deletion - Rice Mart',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.delete_shop_otp',
            with: [
                'otp'      => $this->otp,
                'name'     => $this->name,
                'shopName' => $this->shopName,
            ],
        );
    }
}