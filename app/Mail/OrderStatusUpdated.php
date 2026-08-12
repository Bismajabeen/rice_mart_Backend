<?php
// app/Mail/OrderStatusUpdated.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $subjects = [
            'processing' => 'Your order is being processed',
            'shipped'    => 'Your order has been shipped',
            'delivered'  => 'Your order has been delivered',
            'cancelled'  => 'Your order has been cancelled',
        ];

        return $this->subject($subjects[$this->order->status] ?? 'Order status update')
            ->view('emails.order-status');
    }
}
