{{-- resources/views/emails/order-status.blade.php --}}
<h2>Hi {{ $order->customer_name }},</h2>

@if($order->status === 'processing')
    <p>Your order <strong>{{ $order->order_number }}</strong> is now being processed.</p>
@elseif($order->status === 'shipped')
    <p>Good news! Your order <strong>{{ $order->order_number }}</strong> has been shipped.</p>
@elseif($order->status === 'delivered')
    <p>Your order <strong>{{ $order->order_number }}</strong> has been delivered. Enjoy!</p>
@elseif($order->status === 'cancelled')
    <p>Your order <strong>{{ $order->order_number }}</strong> has been cancelled.</p>
@endif

<p>Total: Rs. {{ number_format($order->total_price, 2) }}</p>
<p>Thank you for shopping with RiceMart.</p>
