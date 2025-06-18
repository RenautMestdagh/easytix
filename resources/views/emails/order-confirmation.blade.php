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

        .detail-item svg {
            margin-right: 15px;
            color: #667eea;
            flex-shrink: 0;
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

        .download-btn svg {
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

        .status-completed svg {
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

        .manage-btn svg {
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ $event->date->format('F j, Y, g:i A') }}</span>
                </div>

                <div class="detail-item">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>{{ $event->location }}</span>
                </div>
            </div>

            <a href="{{ route('tickets.download', [$event->organization->subdomain, $order->uniqid]) }}" class="download-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
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
            <tr class="total-row">
                <td colspan="2"><strong>Total</strong></td>
                <td><strong>€ {{ number_format($orderTotal / 100, 2) }} EUR</strong></td>
            </tr>
            </tbody>
        </table>

        <div class="manage-order">
            <p>Need to make changes or have questions about your order?</p>
            <a href="{{ route('orders.show', [$event->organization->subdomain, $order->uniqid]) }}" class="manage-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
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
