<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Receipt Note — {{ $event->name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Arial', sans-serif;
      font-size: 11pt;
      line-height: 1.4;
      color: #0f0f0f;
      padding: 20px;
    }
    .header {
      display: table;
      width: 100%;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 2pt solid #185FA5;
    }
    .header-left {
      display: table-cell;
      width: 50%;
      vertical-align: middle;
    }
    .header-right {
      display: table-cell;
      width: 50%;
      text-align: right;
      vertical-align: middle;
    }
    .logo {
      width: 60px;
      height: 60px;
    }
    .doc-title {
      font-size: 14pt;
      font-weight: bold;
      color: #185FA5;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    /* QR Session Block */
    .qr-block {
      width: 100%;
      margin: 20px 0;
      border: 2pt solid #185FA5;
      border-radius: 4px;
    }
    .qr-block-inner {
      display: table;
      width: 100%;
    }
    .qr-block-left {
      display: table-cell;
      width: 45%;
      padding: 16px;
      text-align: center;
      border-right: 1pt solid #185FA5;
      vertical-align: middle;
    }
    .qr-code svg {
      width: 180px;
      height: 180px;
      display: block;
      margin: 0 auto;
    }
    .qr-ref {
      font-size: 9pt;
      color: #666;
      margin-top: 8px;
      font-family: 'Courier New', monospace;
    }
    .qr-block-right {
      display: table-cell;
      width: 55%;
      padding: 16px;
      vertical-align: middle;
    }
    .scan-title {
      font-size: 11pt;
      font-weight: 700;
      color: #185FA5;
      margin-bottom: 12px;
    }
    .scan-steps {
      font-size: 9pt;
      color: #333;
      line-height: 1.8;
    }
    .scan-steps div {
      margin-bottom: 2px;
    }
    .session-expires {
      margin-top: 12px;
      padding: 8px;
      background: #e6f1fb;
      border-radius: 4px;
    }
    .session-expires-label {
      font-size: 8pt;
      color: #666;
    }
    .session-expires-value {
      font-size: 9pt;
      font-weight: 600;
      color: #333;
    }

    /* Event Info Block */
    .event-info {
      margin: 20px 0;
      padding: 14px;
      background: #f8f7f5;
      border-left: 4px solid #185FA5;
    }
    .event-name {
      font-size: 14pt;
      font-weight: bold;
      color: #0f0f0f;
      margin-bottom: 8px;
    }
    .event-meta {
      font-size: 9pt;
      color: #5c5550;
      margin-bottom: 2px;
    }
    .event-meta strong {
      color: #0f0f0f;
    }

    /* Section Headers */
    h2 {
      font-size: 11pt;
      margin: 20px 0 10px 0;
      padding-bottom: 6px;
      border-bottom: 1pt solid #ece8e3;
      color: #0f0f0f;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      font-weight: 700;
    }

    /* Tables */
    table.returns-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }
    table.returns-table th {
      background: #0f0f0f;
      color: #ffffff;
      padding: 8px 6px;
      text-align: left;
      font-size: 8pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    table.returns-table td {
      padding: 8px 6px;
      border-bottom: 1pt solid #f0ece8;
      font-size: 9pt;
      vertical-align: top;
    }
    table.returns-table tr:nth-child(even) {
      background: #faf8f6;
    }
    .pieces-cell {
      font-family: 'Courier New', monospace;
      font-size: 8pt;
      color: #5c5550;
      line-height: 1.6;
    }

    /* Borrowed/Operational Items */
    table.simple-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }
    table.simple-table th {
      background: #f0ece8;
      color: #0f0f0f;
      padding: 8px 6px;
      text-align: left;
      font-size: 8pt;
      font-weight: bold;
      border-bottom: 1pt solid #ece8e3;
    }
    table.simple-table td {
      padding: 8px 6px;
      border-bottom: 1pt solid #f5f1ed;
      font-size: 9pt;
    }
    .fill-line {
      display: inline-block;
      border-bottom: 1pt solid #ccc;
      min-width: 60px;
      height: 14px;
    }

    /* Footer */
    .footer {
      position: fixed;
      bottom: 20px;
      left: 20px;
      right: 20px;
      text-align: center;
      font-size: 8pt;
      color: #a09890;
      border-top: 1pt solid #ece8e3;
      padding-top: 8px;
    }
    .footer-line {
      margin-bottom: 2px;
    }
  </style>
</head>
<body>

  {{-- HEADER --}}
  <div class="header">
    <div class="header-left">
      @if(file_exists(public_path('images/grey-apple-events-logo.png')))
        <img src="{{ public_path('images/grey-apple-events-logo.png') }}" alt="Logo" class="logo">
      @endif
    </div>
    <div class="header-right">
      <div class="doc-title">Receipt Note — Return</div>
    </div>
  </div>

  {{-- QR SESSION BLOCK --}}
  <div class="qr-block">
    <div class="qr-block-inner">
      <div class="qr-block-left">
        <div class="qr-code">{!! $qrCodeSvg !!}</div>
        <div class="qr-ref">{{ $receiveSession->receive_ref }}</div>
      </div>
      <div class="qr-block-right">
        <div class="scan-title">SCAN TO BEGIN RECEIVING</div>
        <div class="scan-steps">
          <div>① Open your phone camera</div>
          <div>② Point at the QR code</div>
          <div>③ Log in when prompted</div>
          <div>④ Scan each item as it arrives</div>
          <div>⑤ Select condition and destination</div>
        </div>
        <div class="session-expires">
          <div class="session-expires-label">Session expires:</div>
          <div class="session-expires-value">
            {{ $receiveSession->expires_at->format('d M Y \a\t H:i') }} EAT
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- EVENT INFO --}}
  <div class="event-info">
    <div class="event-name">{{ $event->name }}</div>
    <div class="event-meta"><strong>Dispatch Ref:</strong> {{ $event->plan_ref ?? 'N/A' }}</div>
    <div class="event-meta"><strong>Receive Ref:</strong> {{ $receiveSession->receive_ref }}</div>
    <div class="event-meta"><strong>Set Down Date:</strong> {{ optional($event->setdown_date)->format('d M Y') }}</div>
  </div>

  {{-- SECTION A — OWN INVENTORY EXPECTED RETURNS --}}
  @php
    $ownInventoryDispatches = $event->eventPieceDispatches->groupBy(function($dispatch) {
      return $dispatch->itemPiece->item->category;
    });
  @endphp

  @if($ownInventoryDispatches->isNotEmpty())
    <h2>Section A — Own Inventory Expected Returns</h2>
    <table class="returns-table">
      <thead>
        <tr>
          <th style="width:35%">Item Name</th>
          <th style="width:20%">Category</th>
          <th style="width:15%;text-align:center">Pieces Dispatched</th>
          <th style="width:30%">Piece Codes</th>
        </tr>
      </thead>
      <tbody>
        @foreach($ownInventoryDispatches as $category => $dispatches)
          @php
            $itemDispatches = $dispatches->groupBy('item_piece.item_id');
          @endphp
          @foreach($itemDispatches as $itemId => $pieces)
            @php
              $item = $pieces->first()->itemPiece->item;
              $pieceCodes = $pieces->map(fn($d) => $d->itemPiece->unique_code)->join(', ');
            @endphp
            <tr>
              <td>{{ $item->name }}</td>
              <td>{{ $category }}</td>
              <td style="text-align:center">{{ $pieces->count() }}</td>
              <td class="pieces-cell">{{ $pieceCodes }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  @endif

  {{-- SECTION B — BORROWED ITEMS --}}
  @if($event->borrowedItems->isNotEmpty())
    <h2>Section B — Borrowed Items</h2>
    <table class="simple-table">
      <thead>
        <tr>
          <th style="width:40%">Item</th>
          <th style="width:25%">Source Company</th>
          <th style="width:17%;text-align:center">Qty Dispatched</th>
          <th style="width:18%;text-align:center">Qty Returned</th>
        </tr>
      </thead>
      <tbody>
        @foreach($event->borrowedItems as $borrowed)
          <tr>
            <td>{{ $borrowed->item_name }}</td>
            <td>{{ $borrowed->source_company }}</td>
            <td style="text-align:center">{{ $borrowed->quantity }}</td>
            <td style="text-align:center"><span class="fill-line"></span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  {{-- SECTION C — OPERATIONAL ITEMS --}}
  @if($event->operationalItems->isNotEmpty())
    <h2>Section C — Operational Items</h2>
    <table class="simple-table">
      <thead>
        <tr>
          <th style="width:60%">Item</th>
          <th style="width:20%;text-align:center">Qty Dispatched</th>
          <th style="width:20%;text-align:center">Qty Returned</th>
        </tr>
      </thead>
      <tbody>
        @foreach($event->operationalItems as $operational)
          <tr>
            <td>{{ $operational->operationalItem->name }}</td>
            <td style="text-align:center">{{ $operational->quantity }}</td>
            <td style="text-align:center"><span class="fill-line"></span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  {{-- FOOTER --}}
  <div class="footer">
    <div class="footer-line">Generated by GAIMS — Grey Apple Inventory Management System</div>
    <div class="footer-line">joseasoftwares.co.ke</div>
    <div class="footer-line">Page <span class="pagenum"></span></div>
  </div>

</body>
</html>
