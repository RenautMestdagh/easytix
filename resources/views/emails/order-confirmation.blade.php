<!DOCTYPE html>
<html>
<head>
    <title>Your Order Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: gray;
            text-align: center;
            padding: 40px 30px;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header .success-icon {
            font-size: 48px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 30px;
            color: #34495e;
        }

        .event-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #e1e8ed;
            position: relative;
            overflow: hidden;
        }

        .event-image {
            height: 300px;
            object-fit: contain;
            border-radius: 12px;
            margin-bottom: 50px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .event-card h2 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .event-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .detail-item img {
            margin-right: 15px;
        }

        .detail-item span {
            font-weight: 500;
            color: #2c3e50;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: gray;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .download-btn img {
            margin-right: 10px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }

        .order-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .order-details-table th,
        .order-details-table td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #e8f4fd;
        }

        .order-details-table th {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-details-table td {
            font-weight: 500;
            color: #34495e;
        }

        .status-completed {
            display: flex;
            align-items: center;
            color: #27ae60;
            font-weight: 600;
        }

        .status-completed img {
            margin-right: 8px;
        }

        .order-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .order-summary th,
        .order-summary td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #e8f4fd;
        }

        .order-summary th {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-summary td {
            font-weight: 500;
            color: #34495e;
        }

        .total-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: gray;
            font-weight: 700;
            font-size: 16px;
        }

        .total-row td {
            color: gray;
            border-bottom: none;
        }

        .manage-order {
            text-align: center;
            margin-top: 40px;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
            border-radius: 16px;
        }

        .manage-order p {
            margin-bottom: 20px;
            color: #34495e;
            font-size: 16px;
        }

        .manage-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: gray;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
        }

        .manage-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.5);
        }

        .manage-btn img {
            margin-right: 10px;
        }

        .footer {
            background: #2c3e50;
            color: #bdc3c7;
            text-align: center;
            padding: 30px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 20px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .event-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <div class="success-icon">🎉</div>
        <h1>Order Complete!</h1>
    </div>

    <div class="content">
        <div class="greeting">
            <p>Dear {{ $order->customer->first_name }} {{ $order->customer->last_name }},</p>
            <p>Your order for <strong>{{ $event->name }}</strong> has been successfully processed. Get ready for an amazing experience!</p>
        </div>

        <div class="event-card">
            @if($event->event_image)
                <div style="width: 100%; display: flex; justify-content: center;">
                    <img src="{{ $event->event_image_url }}" alt="{{ $event->name }}" class="event-image">
                </div>

            @endif

            <h2>{{ $event->name }}</h2>

            <div class="event-details">
                <div class="detail-item">
                    <img src="{{ asset('images/email-icons/clock-icon.png') }}" alt="Date" width="24" height="24">
                    <span>{{ $event->date->format('F j, Y, g:i A') }}</span>
                </div>

                @if($event->venue)
                <div class="detail-item">
                    <img src="{{ asset('images/email-icons/maps-icon.png') }}" alt="Location" width="24" height="24">
                    @if(!empty($event->venue->coordinates))
                        <a href="{{ $event->venue->getGoogleMapsUrl() }}"
                           target="_blank"
                           class="hover:underline"
                           style="text-decoration: none; color: #2c3e50;"
                           title="{{ __('View on Google Maps') }}"
                        >
                            {{ Str::limit($event->venue->name, 50, '...') }}
                        </a>
                    @else
                        {{ Str::limit($event->venue->name, 50, '...') }}
                    @endif
                </div>
                @endif
            </div>

            <a href="{{ route('tickets.download', [$event->organization->subdomain, $order->uniqid]) }}" class="download-btn">
                <img src="{{ asset('images/email-icons/download-icon.png') }}" alt="Download" width="24" height="24">
                Download E-Ticket
            </a>
        </div>

        <h3 class="section-title">Order Details</h3>
        <table class="order-details-table">
            <tr>
                <th>Order Number</th>
                <td><strong>{{ $order->uniqid }}</strong></td>
            </tr>
            <tr>
                <th>Transaction ID</th>
                <td>****{{ substr($order->payment_id, -4) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td class="status-completed">
                    <img src="{{ asset('images/email-icons/completed-icon.png') }}" alt="Status" width="24" height="24">
                    Completed
                </td>
            </tr>
        </table>

        <h3 class="section-title">Order Summary</h3>
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

                @if(count($appliedDiscounts) > 0)
                    <tr class="total-row">
                        <td colspan="2"><strong>Subtotal</strong></td>
                        <td><strong>€ {{ number_format($subtotal / 100, 2) }} EUR</strong></td>
                    </tr>

                    <tr>
                        <td colspan="2">Discount</td>
                        <td>-€{{ number_format($discountAmount / 100, 2) }}</td>
                    </tr>
               @endif
                <tr class="total-row">
                    <td colspan="2"><strong>Total</strong></td>
                    <td><strong>€ {{ number_format($orderTotal / 100, 2) }} EUR</strong></td>
                </tr>

            </tbody>
        </table>

        <div class="manage-order">
            <p>Need to make changes or have questions about your order?</p>
            <a href="{{ route('orders.show', [$event->organization->subdomain, $order->uniqid]) }}" class="manage-btn">
                <img src="{{ asset('images/email-icons/gear-icon.png') }}" alt="Manage" width="24" height="24">
                Manage Order
            </a>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your purchase! We can't wait to see you at the event.</p>
    </div>
</div>
</body>
</html>
