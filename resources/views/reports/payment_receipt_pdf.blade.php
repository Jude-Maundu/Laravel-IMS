<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt - {{ $event->plan_ref }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 12px; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #CC0000; padding-bottom: 10px; }
        .logo { height: 60px; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #CC0000; text-transform: uppercase; margin: 0; }
        .receipt-info { margin-bottom: 30px; }
        .receipt-info td { vertical-align: top; padding: 5px 0; }
        .label { font-weight: bold; color: #555; width: 120px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background: #f8f8f8; border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table td { border: 1px solid #ddd; padding: 10px; }
        .total-box { background: #fafafa; border: 1px solid #ddd; padding: 15px; text-align: right; }
        .total-amount { font-size: 18px; font-weight: bold; color: #CC0000; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
        .paid-stamp { 
            position: absolute; top: 150px; right: 50px; 
            border: 4px solid #3B6D11; color: #3B6D11; 
            font-size: 40px; font-weight: bold; padding: 10px 20px; 
            transform: rotate(-15deg); opacity: 0.2; border-radius: 10px; 
        }
    </style>
</head>
<body>
    <div class="paid-stamp">PAID</div>

    <div class="header">
        <img src="{{ public_path('images/grey-apple-events-logo.png') }}" class="logo">
        <p class="title">Payment Receipt</p>
    </div>

    <table class="receipt-info" width="100%">
        <tr>
            <td width="50%">
                <table>
                    <tr><td class="label">Receipt No:</td><td>{{ $ref_no }}</td></tr>
                    <tr><td class="label">Date:</td><td>{{ date('d M Y H:i') }}</td></tr>
                    <tr><td class="label">M-Pesa Ref:</td><td><strong>{{ $event->transaction_id }}</strong></td></tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    <tr><td class="label">Customer:</td><td>{{ $event->client_name }}</td></tr>
                    <tr><td class="label">Phone:</td><td>{{ $event->customer_phone }}</td></tr>
                    <tr><td class="label">Booking Ref:</td><td>{{ $event->plan_ref }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <h3 style="border-bottom: 1px solid #eee; padding-bottom: 5px;">Event Details</h3>
    <table width="100%" style="margin-bottom: 20px;">
        <tr>
            <td class="label">Event Name:</td><td>{{ $event->name }}</td>
            <td class="label">Event Date:</td><td>{{ $event->event_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">Venue:</td><td colspan="3">{{ $event->venue }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th width="100" style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Event Equipment Hire & Services for {{ $event->name }}</td>
                <td style="text-align: right;">KES {{ number_format($event->amount_due ?? $event->cost, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <span style="font-size: 14px; color: #555;">Total Amount Paid:</span><br>
        <span class="total-amount">KES {{ number_format($event->amount_due ?? $event->cost, 2) }}</span>
    </div>

    <div style="margin-top: 30px;">
        <p><strong>Notes:</strong> This is a computer-generated receipt and does not require a signature. Thank you for choosing Grey Apple Events.</p>
    </div>

    <div class="footer">
        Grey Apple Events Limited • Ruaraka, Nairobi, Kenya<br>
        Email: info@greyapple.co.ke • Phone: +254 7XX XXX XXX<br>
        <em>Providing premium event solutions since 2020</em>
    </div>
</body>
</html>
