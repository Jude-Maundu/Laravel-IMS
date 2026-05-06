<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receive Note — {{ $event->name }}</title>
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
        .status-badge {
            display: inline-block;
            background: #e6f1fb;
            color: #185FA5;
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 3px;
            border: 1px solid #c7e2f7;
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
        .info-banner {
            background: #e6f1fb;
            border-left: 4px solid #185FA5;
            padding: 10px 12px;
            margin-bottom: 20px;
            font-size: 10px;
            color: #185FA5;
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
        .piece-code {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #5c5550;
            padding-left: 20px;
        }
        .summary-box {
            background: #fff;
            border: 2px solid #ece8e3;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: inline-block;
            width: 48%;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .summary-label {
            font-weight: bold;
            color: #5c5550;
            font-size: 9px;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 13px;
            font-weight: 700;
            color: #0f0f0f;
        }
        .pagenum:before {
            content: counter(page);
        }
        .qr-code-section {
            margin: 20px 0;
            text-align: center;
            padding: 15px;
            background: #f8f7f5;
            border-radius: 5px;
        }
        .qr-code-section svg {
            max-width: 180px;
            height: auto;
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
        Grey Apple IMS — Receive Note | Page <span class="pagenum"></span>
    </footer>

    <div class="content">
        <div class="report-title">
            RECEIVE NOTE
            <span class="status-badge">PENDING RECEIVAL</span>
        </div>

        <div class="meta-section">
            <div class="meta-item">
                <div class="meta-label">Receive Reference</div>
                <div>{{ $receiveRef }}</div>
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
                <div class="meta-label">Event Date</div>
                <div>{{ $event->event_date->format('D, j M Y') }}</div>
            </div>
            <div class="meta-item" style="margin-top:10px">
                <div class="meta-label">Session Created</div>
                <div>{{ now()->format('d M Y, H:i') }}</div>
            </div>
            <div class="clear"></div>
        </div>

        {{-- QR CODE SECTION --}}
        <div class="qr-code-section">
            <div style="font-size:11px; font-weight:700; color:#0f0f0f; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.05em;">Scan to Begin Receiving</div>
            {!! $qrCodeSvg !!}
            <div style="margin-top:10px; font-size:10px; color:#5c5550; font-family:'Courier New',monospace;">
                Session Token: {{ $receiveSession->session_token }}
            </div>
            <div style="font-size:9px; color:#a09890; margin-top:4px;">
                Expires: {{ $receiveSession->expires_at->format('d M Y, H:i') }} EAT
            </div>
        </div>

        <div class="info-banner">
            <strong>RECEIVE SESSION ACTIVE:</strong> This document lists all items dispatched for {{ $event->name }}. Scan the QR code above on a mobile device to begin receiving. Each piece must be scanned and assigned a condition + destination.
        </div>

        {{-- RECEIVE SUMMARY --}}
        <div class="summary-box">
            <div class="summary-row">
                <div class="summary-label">Total Pieces to Receive</div>
                <div class="summary-value">{{ $totalPiecesDispatched }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Item Types</div>
                <div class="summary-value">{{ $totalItems }}</div>
            </div>
            <div class="summary-row" style="margin-top:10px">
                <div class="summary-label">Borrowed Items</div>
                <div class="summary-value">{{ $totalBorrowed }}</div>
            </div>
            <div class="summary-row" style="margin-top:10px">
                <div class="summary-label">Operational Items</div>
                <div class="summary-value">{{ $totalOperational }}</div>
            </div>
        </div>

        {{-- OWN INVENTORY --}}
        @if($dispatchedItems->count() > 0)
        <div class="section-header">Grey Apple Inventory — Pieces Dispatched</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%; text-align:center;">#</th>
                    <th style="width: 10%;">Code</th>
                    <th style="width: 32%;">Item Name</th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 10%; text-align:center;">Qty Dispatched</th>
                    <th style="width: 25%;">Piece Codes</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grouped = $dispatchedItems->groupBy(fn($ei) => $ei->item->category ?? 'Other');
                    $itemNumber = 1;
                @endphp
                @foreach($grouped as $category => $items)
                    <tr>
                        <td colspan="6" class="category-header">{{ $category }}</td>
                    </tr>
                    @foreach($items as $eventItem)
                        @php
                            $item = $eventItem->item;
                            // Get dispatched pieces for this item
                            $dispatchedPieces = \App\Models\EventPieceDispatch::where('event_id', $event->id)
                                ->whereHas('itemPiece', fn($q) => $q->where('item_id', $item->id))
                                ->with('itemPiece')
                                ->get();
                            $pieceCodes = $dispatchedPieces->pluck('itemPiece.unique_code')->take(5)->implode(', ');
                            if ($dispatchedPieces->count() > 5) {
                                $pieceCodes .= ' +' . ($dispatchedPieces->count() - 5) . ' more';
                            }
                        @endphp
                        <tr>
                            <td style="text-align:center; font-weight:700; font-size:11px; color:#5c5550;">{{ $itemNumber++ }}</td>
                            <td style="font-family: 'Courier New', monospace; font-weight:600; font-size:9px;">
                                {{ strtoupper(substr($item->category ?? 'ITM', 0, 3)) }}-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="font-weight:600;">{{ $item->name }}</td>
                            <td style="color:#5c5550; font-size:11px;">{{ $item->category }}</td>
                            <td style="font-weight:700; font-size:13px; text-align:center; color:#185FA5;">{{ $eventItem->quantity_dispatched }}</td>
                            <td class="piece-code">{{ $pieceCodes }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- BORROWED ITEMS --}}
        @if($event->borrowedItems->where('quantity_dispatched', '>', 0)->count() > 0)
        <div class="section-header">Borrowed Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%; text-align:center;">#</th>
                    <th style="width: 50%;">Item Name</th>
                    <th style="width: 32%;">Source Company</th>
                    <th style="width: 15%; text-align:center;">Qty to Receive</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->borrowedItems->where('quantity_dispatched', '>', 0) as $index => $borrowed)
                <tr>
                    <td style="text-align:center; font-weight:700; font-size:11px; color:#5c5550;">{{ $index + 1 }}</td>
                    <td>{{ $borrowed->item_name }}</td>
                    <td style="color:#5c5550;">{{ $borrowed->source_company }}</td>
                    <td style="font-weight:700; font-size:13px; text-align:center;">{{ $borrowed->quantity_dispatched }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- OPERATIONAL ITEMS --}}
        @if($event->operationalItems->where('quantity_dispatched', '>', 0)->count() > 0)
        <div class="section-header">Operational Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%; text-align:center;">#</th>
                    <th style="width: 72%;">Item Name</th>
                    <th style="width: 25%; text-align:center;">Qty to Receive</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->operationalItems->where('quantity_dispatched', '>', 0) as $index => $opItem)
                <tr>
                    <td style="text-align:center; font-weight:700; font-size:11px; color:#5c5550;">{{ $index + 1 }}</td>
                    <td>{{ $opItem->operationalItem->name ?? $opItem->custom_name }}</td>
                    <td style="font-weight:700; font-size:13px; text-align:center;">{{ $opItem->quantity_dispatched }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div style="margin-top: 50px;">
            <div style="float: left; width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 10px;">
                Received By
            </div>
            <div style="float: right; width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 10px;">
                Verified By (Warehouse Manager)
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin-top: 40px; padding: 12px; background: #f8f7f5; border-radius: 5px; font-size: 10px; color: #5c5550; line-height: 1.6;">
            <strong>Receiving Instructions:</strong> Scan each item piece QR code using the mobile receive interface. Select condition on return (1-5) and assign destination: Warehouse (ready for reuse), Cleaning Bay (requires cleaning), or Repair Workshop (damaged/needs repair). Verify all borrowed and operational items manually. Complete the session when all items are accounted for.
        </div>
    </div>
</body>
</html>
