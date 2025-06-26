<!DOCTYPE html>
<html>
<head>
    <title>Your Upcoming Event Tickets</title>
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
            background: #f5f7fa;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            margin-bottom: 20px;
        }

        .no-events {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .event-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e1e8ed;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .event-card h2 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .event-details {
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .detail-label {
            font-weight: 500;
            color: #7f8c8d;
            min-width: 100px;
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        .tickets-table th, .tickets-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        .tickets-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            margin: 0 5px;
        }

        .footer {
            background: #ecf0f1;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .event-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>Your Upcoming Event Tickets</h1>
    </div>

    <div class="content">
        <div class="greeting">
            <p>Hello,</p>
            <p>Here are your upcoming events and ticket information:</p>
        </div>

        @if(!$hasUpcomingEvents)
            <div class="no-events">
                <p>We couldn't find any upcoming events associated with your email address.</p>
            </div>
        @else
            @foreach($orders as $order)
                <div class="event-card">
                    <h2>{{ $order->event->name }}</h2>

                    <div class="event-details">
                        <div class="detail-item">
                            <span class="detail-label">Date:</span>
                            <span>{{ $order->event->date->format('F j, Y, g:i A') }}</span>
                        </div>
                        @if($order->event->venue)
                            <div class="detail-item">
                                <span class="detail-label">Location:</span>
                                <span>{{ $order->event->venue->name }}</span>
                            </div>
                        @endif
                        <div class="detail-item">
                            <span class="detail-label">Order #:</span>
                            <span>{{ $order->uniqid }}</span>
                        </div>
                    </div>

                    <table class="tickets-table">
                        <thead>
                        <tr>
                            <th>Ticket Type</th>
                            <th>Quantity</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->tickets->groupBy('ticket_type_id') as $ticketTypeId => $tickets)
                            <tr>
                                <td>{{ $tickets->first()->ticketType->name }}</td>
                                <td>{{ $tickets->count() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="action-buttons">
                        <a href="{{ route('tickets.download', [$order->event->organization->subdomain, $order->uniqid]) }}" class="btn">
                            Download Tickets
                        </a>
                        <a href="{{ route('orders.show', [$order->event->organization->subdomain, $order->uniqid]) }}" class="btn">
                            View Order
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="footer">
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
