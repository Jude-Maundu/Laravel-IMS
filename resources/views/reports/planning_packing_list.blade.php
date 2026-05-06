<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Planning Packing List (DRAFT) — {{ $event->name }}</title>
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
        .draft-badge {
            display: inline-block;
            background: #faeeda;
            color: #854F0B;
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 3px;
            border: 1px solid #f5e5c4;
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
        .info-banner svg {
            vertical-align: middle;
            margin-right: 6px;
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
        Grey Apple IMS — Planning Packing List (Draft) | Page <span class="pagenum"></span>
    </footer>

    <div class="content">
        <div class="report-title">
            PACKING LIST
            <span class="draft-badge">DRAFT</span>
        </div>

        <div class="meta-section">
            <div class="meta-item">
                <div class="meta-label">Plan Reference</div>
                <div>{{ $planRef }}</div>
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

        <div class="info-banner">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <strong>DRAFT DOCUMENT:</strong> Planning checklist for internal preparation. Final dispatch list will include QR codes.
        </div>

        {{-- OWN INVENTORY --}}
        @if($event->eventItems->count() > 0)
        <div class="section-header">Grey Apple Inventory</div>
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
        @endif

        {{-- BORROWED ITEMS --}}
        @if($event->borrowedItems->count() > 0)
        <div class="section-header">Borrowed Items</div>
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
        @endif

        {{-- OPERATIONAL ITEMS --}}
        @if($event->operationalItems->count() > 0)
        <div class="section-header">Operational Items</div>
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
        @endif

        <div style="margin-top: 50px;">
            <div style="float: left; width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 10px;">
                Prepared By
            </div>
            <div style="float: right; width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 10px;">
                Reviewed By
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
