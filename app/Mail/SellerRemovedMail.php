<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerRemovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $sellerName;
    public string $shopName;
    public string $reason;

    public function __construct(string $sellerName, string $shopName, string $reason)
    {
        $this->sellerName = $sellerName;
        $this->shopName = $shopName;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Your Rice Mart Shop Account Has Been Removed')
            ->view('emails.seller_removed');
    }
}