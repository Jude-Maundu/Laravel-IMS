<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.5;
        }

        /* Header */
        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 2pt solid #185FA5;
            margin-bottom: 16px;
        }
        .pdf-logo-text {
            font-size: 14pt;
            font-weight: bold;
            color: #185FA5;
        }
        .pdf-doc-type {
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a1a;
            text-align: right;
        }
        .pdf-doc-ref {
            font-size: 8pt;
            color: #666;
            text-align: right;
        }

        /* Event info block */
        .pdf-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 24px;
            background: #e6f1fb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 16px;
        }
        .pdf-info-label {
            font-size: 7.5pt;
            color: #555;
            font-weight: bold;
            text-transform: uppercase;
        }
        .pdf-info-val {
            font-size: 9pt;
            color: #1a1a1a;
        }

        /* Summary cards */
        .pdf-summary-row {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .pdf-summary-card {
            flex: 1;
            border: 1pt solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }
        .pdf-summary-num {
            font-size: 18pt;
            font-weight: bold;
            line-height: 1;
        }
        .pdf-summary-label {
            font-size: 7.5pt;
            color: #666;
            margin-top: 4px;
        }
        .pdf-card-blue   { border-color: #185FA5; }
        .pdf-card-green  { border-color: #166534; }
        .pdf-card-amber  { border-color: #854d0e; }
        .pdf-card-red    { border-color: #991b1b; }
        .pdf-num-blue    { color: #185FA5; }
        .pdf-num-green   { color: #166534; }
        .pdf-num-amber   { color: #854d0e; }
        .pdf-num-red     { color: #991b1b; }

        /* Section headings */
        .pdf-section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #185FA5;
            border-left: 3pt solid #185FA5;
            padding-left: 8px;
            margin: 16px 0 8px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th {
            background: #185FA5;
            color: #fff;
            font-size: 8pt;
            font-weight: bold;
            padding: 6px 8px;
            text-align: left;
        }
        td {
            padding: 5px 8px;
            font-size: 8.5pt;
            border-bottom: 0.5pt solid #e5e7eb;
        }
        tr:nth-child(even) td { background: #f8fafc; }

        /* Destination badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-warehouse { background: #e6f1fb; color: #185FA5; }
        .badge-cleaning  { background: #faeeda; color: #854d0e; }
        .badge-repair    { background: #fcebeb; color: #991b1b; }
        .badge-missing   { background: #fef9c3; color: #854d0e; }

        /* Missing items section */
        .pdf-missing-section {
            border: 1.5pt solid #991b1b;
            border-radius: 4px;
            padding: 10px;
            margin-top: 16px;
        }
        .pdf-missing-title {
            font-size: 10pt;
            font-weight: bold;
            color: #991b1b;
            margin-bottom: 8px;
        }

        /* Footer */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 0.5pt solid #e5e7eb;
            padding: 6px 0;
            font-size: 7.5pt;
            color: #999;
            display: flex;
            justify-content: space-between;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="pdf-header">
    <div>
        @if(file_exists(public_path('images/grey-apple-events-logo.png')))
            <img src="{{ public_path('images/grey-apple-events-logo.png') }}"
                 style="height:40px;width:auto;" alt="Grey Apple Events">
        @else
            <div class="pdf-logo-text">GREY APPLE EVENTS</div>
        @endif
    </div>
    <div>
        <div class="pdf-doc-type">RECEIVING REPORT</div>
        <div class="pdf-doc-ref">Ref: {{ $event->receive_ref }}</div>
        <div class="pdf-doc-ref">
            Generated: {{ now()->format('d M Y, H:i') }} EAT
        </div>
    </div>
</div>

{{-- EVENT INFO --}}
<div class="pdf-info-grid">
    <div>
        <div class="pdf-info-label">Event</div>
        <div class="pdf-info-val">{{ $event->name }}</div>
    </div>
    <div>
        <div class="pdf-info-label">Client</div>
        <div class="pdf-info-val">{{ $event->client_name }}</div>
    </div>
    <div>
        <div class="pdf-info-label">Venue</div>
        <div class="pdf-info-val">{{ $event->venue }}</div>
    </div>
    <div>
        <div class="pdf-info-label">Set Down Date</div>
        <div class="pdf-info-val">
            {{ optional($event->setdown_date)->format('d M Y') }}
        </div>
    </div>
    <div>
        <div class="pdf-info-label">Dispatch Ref</div>
        <div class="pdf-info-val">{{ $event->plan_ref ?? '—' }}</div>
    </div>
    <div>
        <div class="pdf-info-label">Receive Ref</div>
        <div class="pdf-info-val">{{ $event->receive_ref }}</div>
    </div>
</div>

{{-- SUMMARY CARDS --}}
<div class="pdf-summary-row">
    <div class="pdf-summary-card pdf-card-blue">
        <div class="pdf-summary-num pdf-num-blue">
            {{ $event->eventPieceDispatches->count() }}
        </div>
        <div class="pdf-summary-label">Pieces Dispatched</div>
    </div>
    <div class="pdf-summary-card pdf-card-green">
        <div class="pdf-summary-num pdf-num-green">{{ $toWarehouse }}</div>
        <div class="pdf-summary-label">To Warehouse</div>
    </div>
    <div class="pdf-summary-card pdf-card-amber">
        <div class="pdf-summary-num pdf-num-amber">{{ $toCleaning }}</div>
        <div class="pdf-summary-label">To Cleaning</div>
    </div>
    <div class="pdf-summary-card pdf-card-red">
        <div class="pdf-summary-num pdf-num-red">{{ $toRepair }}</div>
        <div class="pdf-summary-label">To Repair</div>
    </div>
    <div class="pdf-summary-card" style="border-color:#854d0e">
        <div class="pdf-summary-num" style="color:#854d0e">
            {{ $missingItems->count() }}
        </div>
        <div class="pdf-summary-label">Missing</div>
    </div>
</div>

{{-- SECTION A: RECEIVED ITEMS --}}
<div class="pdf-section-title">A. Received Items</div>

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Piece Code</th>
            <th>Condition</th>
            <th>Destination</th>
            <th>Damage Note</th>
        </tr>
    </thead>
    <tbody>
        @forelse($receivedPieces as $itemId => $pieces)
            @foreach($pieces as $piece)
            <tr>
                <td>{{ $piece->item->name }}</td>
                <td>{{ $piece->item->category }}</td>
                <td style="font-family:monospace">{{ $piece->unique_code }}</td>
                <td>
                    @if($piece->condition_score)
                        {{ $piece->condition_score }}/5
                    @else
                        —
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $piece->destination }}">
                        {{ ucfirst($piece->destination) }}
                    </span>
                </td>
                <td>{{ $piece->damage_note ?? '—' }}</td>
            </tr>
            @endforeach
        @empty
            <tr><td colspan="6" style="text-align:center;color:#999">
                No pieces recorded via scan session
            </td></tr>
        @endforelse
    </tbody>
</table>

{{-- SECTION B: BORROWED ITEMS --}}
@if($event->borrowedItems->count() > 0)
<div class="pdf-section-title">B. Borrowed Items</div>
<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Source Company</th>
            <th>Qty Dispatched</th>
            <th>Qty Returned</th>
            <th>Shortfall</th>
        </tr>
    </thead>
    <tbody>
        @foreach($event->borrowedItems as $borrowed)
        @php
            $returned   = $borrowed->quantity_returned ?? 0;
            $shortfall  = $borrowed->quantity_dispatched - $returned;
        @endphp
        <tr>
            <td>{{ $borrowed->item_name }}</td>
            <td>{{ $borrowed->source_company ?? '—' }}</td>
            <td>{{ $borrowed->quantity_dispatched }}</td>
            <td>{{ $returned }}</td>
            <td>
                @if($shortfall > 0)
                    <span style="color:#991b1b;font-weight:bold">
                        {{ $shortfall }} missing
                    </span>
                @else
                    <span style="color:#166534">✓ Complete</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECTION C: OPERATIONAL ITEMS --}}
@if($event->operationalItems->count() > 0)
<div class="pdf-section-title">C. Operational Items</div>
<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty Dispatched</th>
            <th>Qty Returned</th>
            <th>Shortfall</th>
        </tr>
    </thead>
    <tbody>
        @foreach($event->operationalItems as $opItem)
        @php
            $returned  = $opItem->quantity_returned ?? 0;
            $shortfall = $opItem->quantity_dispatched - $returned;
        @endphp
        <tr>
            <td>{{ $opItem->display_name }}</td>
            <td>{{ $opItem->quantity_dispatched }}</td>
            <td>{{ $returned }}</td>
            <td>
                @if($shortfall > 0)
                    <span style="color:#991b1b;font-weight:bold">
                        {{ $shortfall }} missing
                    </span>
                @else
                    <span style="color:#166534">✓ Complete</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECTION D: MISSING ITEMS --}}
@if($missingItems->count() > 0)
<div class="pdf-missing-section">
    <div class="pdf-missing-title">
        ⚠ D. Missing Items ({{ $missingItems->count() }})
    </div>
    <table>
        <thead>
            <tr>
                <th style="background:#991b1b">Item</th>
                <th style="background:#991b1b">Piece Code</th>
                <th style="background:#991b1b">Category</th>
                <th style="background:#991b1b">Notes</th>
                <th style="background:#991b1b">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($missingItems as $missing)
            <tr>
                <td>{{ $missing->item->name }}</td>
                <td style="font-family:monospace">{{ $missing->unique_code }}</td>
                <td>{{ $missing->item->category }}</td>
                <td>{{ $missing->notes ?? '—' }}</td>
                <td>
                    <span class="badge badge-missing">
                        {{ ucfirst($missing->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- FOOTER --}}
<div class="pdf-footer">
    <span>Generated by GAIMS — Grey Apple Inventory Management System</span>
    <span>joseasoftwares.co.ke</span>
    <span>{{ $event->receive_ref }}</span>
</div>

</body>
</html>
