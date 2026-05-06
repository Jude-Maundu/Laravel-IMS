<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Dispatch Packing List (FINAL) — {{ $event->name }}</title>
    <style>
        @page {
            margin: 40px 50px 80px 50px;
        }
        header {
            margin-bottom: 30px;
            border-bottom: 2px solid #CC0000;
            padding-bottom: 20px;
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #b0a8a0;
            border-top: 1px solid #ece8e3;
            padding-top: 10px;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #0f0f0f;
            line-height: 1.5;
        }
        .logo-container {
            float: left;
            width: 200px;
            text-align: left;
        }
        .logo-container img {
            max-width: 180px;
            height: auto;
        }
        .address-container {
            float: right;
            text-align: right;
            width: 350px;
            font-size: 10px;
            color: #5c5550;
            line-height: 1.4;
        }
        .clear {
            clear: both;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #CC0000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .final-badge {
            display: inline-block;
            background: #eaf3de;
            color: #3B6D11;
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 3px;
            border: 1px solid #d4e7c5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 8px;
        }
        .meta-section {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f7f5;
            border-radius: 5px;
        }
        .meta-item {
            display: inline-block;
            width: 32%;
            font-size: 11px;
        }
        .meta-label {
            font-weight: bold;
            color: #a09890;
            text-transform: uppercase;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .qr-section {
            background: #e6f1fb;
            border-left: 4px solid #185FA5;
            padding: 15px;
            margin-bottom: 20px;
        }
        .qr-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .qr-section td {
            padding: 10px;
            vertical-align: middle;
        }
        .qr-code-cell {
            width: 200px;
            text-align: center;
            border-right: 2px solid #cce0f5;
        }
        .qr-instructions {
            padding-left: 20px;
        }
        .qr-title {
            font-size: 13px;
            font-weight: bold;
            color: #185FA5;
            margin-bottom: 10px;
        }
        .qr-steps {
            font-size: 10px;
            color: #185FA5;
            line-height: 1.8;
        }
        .qr-expiry {
            margin-top: 10px;
            padding: 8px;
            background: #fff;
            border-radius: 4px;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f8f7f5;
            color: #5c5550;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ece8e3;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #f5f1ed;
            vertical-align: middle;
        }
        .category-header {
            background: #f8f7f5;
            font-weight: 700;
            color: #0f0f0f;
            font-size: 10px;
            padding: 8px !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-top: 2px solid #ece8e3;
        }
        .section-header {
            font-size: 14px;
            font-weight: bold;
            color: #0f0f0f;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ece8e3;
            padding-bottom: 5px;
        }
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="{{ public_path('images/grey-apple-events-logo.png') }}" alt="Grey Apple Events">
        </div>
        <div class="address-container">
            <strong>Location:</strong> Thome Estate, Mbuni Drive, MD05, Nairobi, Kenya.<br>
            <strong>Alternative:</strong> Ruaraka Housing Estate, 23 USIU Rd, Nairobi.<br>
            <strong>Email:</strong> info@greyapple.co.ke<br>
            <strong>Phone:</strong> +254 722 289648
        </div>
        <div class="clear"></div>
    </header>

    <footer>
        Grey Apple IMS — Dispatch Packing List (Final) | Page <span class="pagenum"></span>
    </footer>

    <div class="content">
        <div class="report-title">
            DISPATCH PACKING LIST
            <span class="final-badge">FINAL</span>
        </div>

        <div class="meta-section">
            <div class="meta-item">
                <div class="meta-label">Dispatch Reference</div>
                <div>{{ $dispatchRef }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Event Name</div>
                <div>{{ $event->name }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Client</div>
                <div>{{ $event->client_name }}</div>
            </div>
            <div class="meta-item" style="margin-top:10px">
                <div class="meta-label">Venue</div>
                <div>{{ $event->venue }}</div>
            </div>
            <div class="meta-item" style="margin-top:10px">
                <div class="meta-label">Loading Date</div>
                <div>{{ $event->loading_date->format('D, j M Y') }}</div>
            </div>
            <div class="meta-item" style="margin-top:10px">
                <div class="meta-label">Event Date</div>
                <div>{{ $event->event_date->format('D, j M Y') }}</div>
            </div>
            <div class="clear"></div>
        </div>

        {{-- QR CODE SECTION --}}
        <div class="qr-section">
            <table>
                <tr>
                    <td class="qr-code-cell">
                        {!! $qrCodeSvg !!}
                        <div style="font-size:9px;color:#185FA5;margin-top:8px;font-family:monospace;">
                            {{ $dispatchRef }}
                        </div>
                    </td>
                    <td class="qr-instructions">
                        <div class="qr-title">SCAN TO BEGIN LOADING</div>
                        <div class="qr-steps">
                            <div>① Open your phone camera</div>
                            <div>② Point at the QR code</div>
                            <div>③ Log in when prompted</div>
                            <div>④ Scan each item as you load</div>
                        </div>
                        <div class="qr-expiry">
                            <div class="meta-label">Session expires:</div>
                            <div style="color:#185FA5;font-weight:600">{{ $sessionExpiresAt }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- OWN INVENTORY --}}
        @if($event->eventItems->count() > 0)
        <div class="section-header">Grey Apple Inventory (Scan Items)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%; text-align:center;">#</th>
                    <th style="width: 8%; text-align:center;">Image</th>
                    <th style="width: 12%;">Code</th>
                    <th style="width: 32%;">Item Name</th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 12%; text-align:center;">Qty Requested</th>
                    <th style="width: 13%; text-align:center;">Available</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grouped = $event->eventItems->groupBy(fn($ei) => $ei->item->category ?? 'Other');
                    $itemNumber = 1;
                @endphp
                @foreach($grouped as $category => $items)
                    <tr>
                        <td colspan="7" class="category-header">{{ $category }}</td>
                    </tr>
                    @foreach($items as $eventItem)
                        @php
                            $item = $eventItem->item;
                            $availableCount = $item->pieces()->where('status', 'Available')->count();
                            $imagePath = $item->primaryImage?->image_path ?? $item->image_path;
                            $fullImagePath = $imagePath ? public_path('storage/' . $imagePath) : null;
                        @endphp
                        <tr>
                            <td style="text-align:center; font-weight:700; font-size:11px; color:#5c5550;">{{ $itemNumber++ }}</td>
                            <td style="text-align:center; padding:5px;">
                                @if($fullImagePath && file_exists($fullImagePath))
                                    <img src="{{ $fullImagePath }}" style="width:40px; height:40px; object-fit:cover; border-radius:4px; border:1px solid #ece8e3;">
                                @else
                                    <div style="width:40px; height:40px; background:#f8f7f5; border:1px solid #ece8e3; border-radius:4px; display:inline-block; line-height:40px; font-size:9px; color:#a09890; font-weight:600;">N/A</div>
                                @endif
                            </td>
                            <td style="font-family: 'Courier New', monospace; font-weight:600; font-size:9px;">
                                {{ strtoupper(substr($item->category ?? 'ITM', 0, 3)) }}-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="font-weight:600;">{{ $item->name }}</td>
                            <td style="color:#5c5550; font-size:11px;">{{ $item->category }}</td>
                            <td style="font-weight:700; font-size:13px; text-align:center; color:#CC0000;">{{ $eventItem->quantity_requested }}</td>
                            <td style="text-align:center; font-size:11px; color:#5c5550;">{{ $availableCount }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <p style="font-size:10px; color:#5c5550; margin-top:8px; font-style:italic;">
            ✓ Scan QR codes on individual pieces as you load them onto the truck.
        </p>
        @endif

        {{-- BORROWED ITEMS --}}
        @if($event->borrowedItems->count() > 0)
        <div class="section-header">Borrowed Items (Manual Count - Do Not Scan)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%; text-align:center;">#</th>
                    <th style="width: 50%;">Item Name</th>
                    <th style="width: 32%;">Source Company</th>
                    <th style="width: 15%; text-align:center;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->borrowedItems as $index => $borrowed)
                <tr>
                    <td style="text-align:center; font-weight:700; font-size:11px; color:#5c5550;">{{ $index + 1 }}</td>
                    <td>{{ $borrowed->item_name }}</td>
                    <td style="color:#5c5550;">{{ $borrowed->source_company }}</td>
                    <td style="font-weight:700; font-size:13px; text-align:center;">{{ $borrowed->quantity_dispatched }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:10px; color:#a09890; margin-top:8px; font-style:italic;">
            ⚠ Borrowed items do not have QR codes. Manually verify quantities before loading.
        </p>
        @endif

        {{-- OPERATIONAL ITEMS --}}
        @if($event->operationalItems->count() > 0)
        <div class="section-header">Operational Items (Manual Count - Do Not Scan)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%; text-align:center;">#</th>
                    <th style="width: 72%;">Item Name</th>
                    <th style="width: 25%; text-align:center;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->operationalItems as $index => $opItem)
                <tr>
                    <td style="text-align:center; font-weight:700; font-size:11px; color:#5c5550;">{{ $index + 1 }}</td>
                    <td>{{ $opItem->operationalItem->name ?? $opItem->custom_name }}</td>
                    <td style="font-weight:700; font-size:13px; text-align:center;">{{ $opItem->quantity_dispatched }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:10px; color:#a09890; margin-top:8px; font-style:italic;">
            ⚠ Operational items are consumables (cable ties, tape, etc.). Count packs/rolls/units manually.
        </p>
        @endif

        <div style="margin-top: 50px;">
            <div style="float: left; width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 10px;">
                Dispatched By
            </div>
            <div style="float: right; width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 10px;">
                Received By
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
