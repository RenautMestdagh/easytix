<!DOCTYPE html>
<html>
<head>
    <title>Your Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .event-info {
            margin-bottom: 30px;
        }
        .icon-text {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .icon-text svg {
            margin-right: 8px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
        }
        .order-details {
            margin: 20px 0;
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th, .order-details td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-summary {
            margin: 30px 0;
            width: 100%;
            border-collapse: collapse;
        }
        .order-summary th, .order-summary td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-summary th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">Your order is complete!</div>

<p>Dear {{ $order->customer->first_name }} {{ $order->customer->last_name }},</p>
<p>Your order for {{ $event->name }} has been successfully processed. Below you will find an overview and details of your order.</p>

<div class="event-info">
    <h2>{{ $event->name }}</h2>

    <div class="icon-text">
        <!-- Clock icon would go here -->
        <span>{{ $event->date->format('F j, Y, g:i A') }}</span>
    </div>

    <div class="icon-text">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 25px; height: 25px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
        </svg>

        <span>{{ $event->location }}</span>
    </div>

    <a href="{{ route('tickets.download', [$event->organization->subdomain, $order->id]) }}" class="button">Download E-Ticket</a>
</div>

<h3>Order details</h3>
<table class="order-details">
    <tr>
        <th>Order number</th>
        <td>{{ $order->id }}</td>
    </tr>
    <tr>
        <th>Transaction id</th>
        <td>****{{ substr($order->payment_intent_id, -4) }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 25px; height: 25px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Completed
        </td>
    </tr>
</table>

<h3>Order summary</h3>
<table class="order-summary">
    <thead>
    <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Subtotal (incl. VAT)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($quantities as $id => $ticketType)
        @if($ticketType->amount == 0) @continue @endif
        <tr>
            <td>{{ $ticketType->name }}</td>
            <td>{{ $ticketType->amount }}</td>
            <td>€ {{ number_format($ticketType->price_cents / 100, 2) }} EUR</td>
        </tr>
    @endforeach
    <tr class="total-row">
        <td colspan="2">Total</td>
        <td>€ {{ number_format($orderTotal / 100, 2) }} EUR</td>
    </tr>
    </tbody>
</table>

<p>View and manage your order on your own <a href="{{ route('orders.show', [$event->organization->subdomain, $order->id]) }}">order page</a>.</p>
<a href="{{ route('orders.show', [$event->organization->subdomain, $order->id]) }}" class="button">Manage Order</a>
</body>
</html>
