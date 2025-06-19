<!DOCTYPE html>
<html>
<head>
    <title>Tickets for Order #{{ $order->uniqid }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #2c3e50;
            padding: 0;
            min-height: 100vh;
        }

        .ticket {
            page-break-after: always;
            max-width: 650px;
            margin: 0 auto 20px auto;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            position: relative;
            border: 2px solid #dee2e6;
        }

        .ticket:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        /* Header with event image */
        .ticket::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 220px;
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            z-index: 1;
        }

        .ticket::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 220px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            z-index: 2;
        }

        .header {
            position: relative;
            z-index: 3;
            text-align: center;
            padding: 20px 30px 30px 30px;
            color: white;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .event-image-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 220px;
            overflow: hidden;
            border-radius: 24px 24px 0 0;
            z-index: 1;
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 4;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .organization-info {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            gap: 12px;
        }

        .organization-logo {
            width: 250px;
            height: 80px;
            border-radius: 8px;
            background: #373737a1;
            padding: 6px;
            object-fit: contain;
        }

        .organization-name {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .event-date-location {
            font-size: 18px;
            margin-bottom: 10px;
            opacity: 0.95;
            font-weight: 500;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.3);
        }

        .ticket-body {
            padding: 40px 30px;
            background: white;
            position: relative;
        }

        /* Decorative notches */
        .ticket-body::before,
        .ticket-body::after {
            content: '';
            position: absolute;
            top: -12px;
            width: 24px;
            height: 24px;
            background: #343a40;
            border-radius: 50%;
        }

        .ticket-body::before {
            left: -12px;
        }

        .ticket-body::after {
            right: -12px;
        }

        .details-section {
            margin-bottom: 40px;
        }

        .ticket-number {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 30px;
            padding: 0;
            background: transparent;
            border: none;
            flex-wrap: wrap;
        }

        .ticket-icon {
            width: 24px;
            aspect-ratio: 1;
        }

        .ticket-id {
            color: #2c3e50;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .detail-item {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: none;
            position: relative;
            overflow: hidden;
        }

        .detail-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #6a11cb, #2575fc);
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }

        .detail-icon {
            margin-left: 15px;
            opacity: 0.8;
            width: 20px;
            aspect-ratio: 1;
        }

        /* Color accents for different items */
        .detail-item.attendee::before {
            background: linear-gradient(to bottom, #ff416c, #ff4b2b);
        }

        .detail-item.order::before {
            background: linear-gradient(to bottom, #4776E6, #8E54E9);
        }

        .detail-item.type::before {
            background: linear-gradient(to bottom, #00c6ff, #0072ff);
        }

        .detail-item.price::before {
            background: linear-gradient(to bottom, #11998e, #38ef7d);
        }

        .qr-section {
            text-align: center;
        }

        .qr-container {
            display: inline-block;
            padding: 25px;
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(52, 58, 64, 0.3);
            position: relative;
        }

        .qr-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #343a40, #495057, #343a40);
            border-radius: 22px;
            z-index: -1;
        }

        .qr-code-placeholder {
            background: white;
            border-radius: 12px;
            padding: 10px;
            color: #2c3e50;
            min-width: 200px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .qr-code-placeholder svg {
            width: 180px;
            height: 180px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .qr-code-text {
            font-size: 14px;
            color: #6c757d;
            word-break: break-all;
            text-align: center;
        }

        .footer {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 1px solid #dee2e6;
        }

        .footer-main {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .footer-secondary {
            font-size: 14px;
            color: #6c757d;
        }

        .scan-icon {
            color: #343a40;
        }

        /* Security features */
        .security-strip {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 8px;
            background: repeating-linear-gradient(
                45deg,
                #343a40,
                #343a40 10px,
                #495057 10px,
                #495057 20px
            );
        }

        .hologram-effect {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            z-index: 4;
        }

        /* Print optimizations */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .ticket {
                box-shadow: none;
                border: 2px solid #ddd;
                margin-bottom: 20px;
                max-width: none;
            }
        }

        @page {
            margin: 0.5in;
            size: A4;
        }
    </style>
</head>
<body>

@foreach($tickets as $ticket)
    <div class="ticket">
        <div class="security-strip"></div>
        <div class="hologram-effect"></div>

        @if($order->event->event_image)
            <div class="event-image-container">
                <img src="{{ $order->event->event_image_url }}" alt="{{ $order->event->name }}" class="event-image">
            </div>
        @endif

        <div class="header">
            <div class="header-content">
                <div class="organization-info">
                    @if($order->event->organization->logo)
                        <img src="{{ $order->event->organization->logo_url }}" alt="{{ $order->event->organization->name }}" class="organization-logo">
                    @else
                        <div class="organization-name">
                            Presented by {{ $order->event->organization->name }}
                        </div>
                    @endif
                </div>

                <h1>{{ $order->event->name }}</h1>
                <div class="event-date-location">
                    {{ $order->event->date->format('F j, Y') }} • {{ $order->event->location }}
                </div>
            </div>
        </div>

        <div class="ticket-body">
            <div class="details-section">
                <div class="ticket-number">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ticket-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span class="ticket-id">Ticket #{{ $ticket->id }}</span>
                </div>

                <div class="detail-grid">
                    <div class="detail-item attendee">
                        <div class="detail-content">
                            <div class="detail-label">Attendee</div>
                            <div class="detail-value">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="detail-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>

                    <div class="detail-item order">
                        <div class="detail-content">
                            <div class="detail-label">Order Number</div>
                            <div class="detail-value" style="font-size: 10px">{{ $order->uniqid }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="detail-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                        </svg>
                    </div>

                    <div class="detail-item type">
                        <div class="detail-content">
                            <div class="detail-label">Ticket Type</div>
                            <div class="detail-value">{{ $ticket->ticketType->name }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="detail-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                        </svg>
                    </div>

                    <div class="detail-item price">
                        <div class="detail-content">
                            <div class="detail-label">Price</div>
                            <div class="detail-value">€{{ number_format($ticket->ticketType->price_cents / 100, 2) }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="detail-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="qr-section">
                <div class="qr-container">
                    <div class="qr-code-placeholder">
                        <div>{!! $ticket->qr_code_image !!}</div>
                        <div class="qr-code-text">{{ $ticket->qr_code }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-main">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="scan-icon" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.375a1.125 1.125 0 0 1 1.125 1.125v.375a1.125 1.125 0 0 1-1.125 1.125H6.75a1.125 1.125 0 0 1-1.125-1.125V7.875A1.125 1.125 0 0 1 6.75 6.75ZM6.75 16.5h.375a1.125 1.125 0 0 1 1.125 1.125v.375A1.125 1.125 0 0 1 7.125 19.5H6.75a1.125 1.125 0 0 1-1.125-1.125v-.375a1.125 1.125 0 0 1 1.125-1.125ZM16.5 6.75h.375a1.125 1.125 0 0 1 1.125 1.125v.375a1.125 1.125 0 0 1-1.125 1.125H16.5a1.125 1.125 0 0 1-1.125-1.125V7.875a1.125 1.125 0 0 1 1.125-1.125Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 13.5h4.5v4.5h-4.5v-4.5Z" />
                </svg>
                Scan this QR code at the event entrance
            </div>
            <div class="footer-secondary">
                For questions, contact {{ $order->event->organization->name }}
            </div>
        </div>
    </div>
@endforeach
</body>
</html>
