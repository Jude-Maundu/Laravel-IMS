<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bulk Labels - {{ $title }}</title>
  @vite(['resources/css/app.css'])
</head>
<body>

@php
  $labelsPerSheet = $size === 'full' ? 8 : ($size === 'medium' ? 18 : 40);
  $totalLabels = $pieces->count();
  $totalSheets = ceil($totalLabels / $labelsPerSheet);

  $sizeData = [
    'full' => ['name' => 'Full', 'dims' => '105 × 74 mm', 'per' => '8 per sheet'],
    'medium' => ['name' => 'Medium', 'dims' => '76 × 40 mm', 'per' => '18 per sheet'],
    'small' => ['name' => 'Small', 'dims' => '50 × 27 mm', 'per' => '40 per sheet'],
  ];

  // Group pieces by item for category print
  $itemGroups = $pieces->groupBy('item_id');
  $isCategoryPrint = $itemGroups->count() > 1;
@endphp

<div class="lbl-screen-only">
  {{-- TOP BAR --}}
  <div class="lbl-topbar">
    <div class="lbl-breadcrumb">
      <a href="javascript:history.back()" class="lbl-breadcrumb-link" style="display:flex;align-items:center;gap:6px">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <path d="M10 14L4 8l6-6"/>
        </svg>
        Back
      </a>
      <span class="lbl-breadcrumb-sep">→</span>
      <span class="lbl-breadcrumb-current">{{ $title }}</span>
      <span class="lbl-breadcrumb-sep">→</span>
      <span class="lbl-breadcrumb-current">Print Labels</span>
    </div>
    <button type="button" class="lbl-print-button" onclick="window.print()">
      <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
        <path d="M4 5V2h8v3M4 11H2V6h12v5h-2M5 9h6v5H5z"/>
      </svg>
      Print All Labels
    </button>
  </div>

  {{-- CONTROL PANEL --}}
  <div class="lbl-control-panel">
    <div class="lbl-control-left">
      <div class="lbl-control-heading">Select Label Size</div>
      <div class="lbl-size-cards">
        @foreach(['full', 'medium', 'small'] as $sizeOption)
          <div class="lbl-size-card {{ $size === $sizeOption ? 'lbl-size-card-active' : '' }}" onclick="changeSize('{{ $sizeOption }}')">
            @if($size === $sizeOption)
              <div class="lbl-size-check">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M3 8l3 3 7-7"/></svg>
              </div>
            @endif
            <div class="lbl-size-card-name">{{ $sizeData[$sizeOption]['name'] }}</div>
            <div class="lbl-size-card-dims">{{ $sizeData[$sizeOption]['dims'] }}</div>
            <div class="lbl-size-card-per">{{ $sizeData[$sizeOption]['per'] }}</div>
            <div class="lbl-size-card-thumb lbl-size-thumb-{{ $sizeOption }}"></div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="lbl-control-right">
      <div class="lbl-print-summary">
        <div class="lbl-summary-row">
          <span class="lbl-summary-label">Total Labels</span>
          <span class="lbl-summary-value">{{ $totalLabels }}</span>
        </div>
        <div class="lbl-summary-row">
          <span class="lbl-summary-label">Sheets Needed</span>
          <span class="lbl-summary-value">{{ $totalSheets }}</span>
        </div>
        <div class="lbl-summary-row">
          <span class="lbl-summary-label">Labels Per Sheet</span>
          <span class="lbl-summary-value">{{ $labelsPerSheet }}</span>
        </div>
        <div class="lbl-summary-note">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><path d="M8 6v4M8 11v1"/></svg>
          Print on A4 sticker paper for best results. For permanent labels use vinyl laminated sticker paper.
        </div>
      </div>
    </div>
  </div>

  {{-- SUMMARY PANEL --}}
  @if($isCategoryPrint)
    <div class="lbl-item-summary-panel">
      <div class="lbl-summary-panel-heading">Category: {{ $title }}</div>
      <table class="lbl-summary-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Piece Count</th>
            <th>Labels to Print</th>
          </tr>
        </thead>
        <tbody>
          @foreach($itemGroups as $itemId => $itemPieces)
            @php $firstPiece = $itemPieces->first(); @endphp
            <tr>
              <td>{{ $firstPiece->item->name }}</td>
              <td>{{ $itemPieces->count() }}</td>
              <td>{{ $itemPieces->count() }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ $totalLabels }}</strong></td>
            <td><strong>{{ $totalLabels }}</strong></td>
          </tr>
        </tfoot>
      </table>
    </div>
  @else
    @php
      $firstPiece = $pieces->first();
      $availableCount = $pieces->where('status', 'Available')->count();
      $assignedCount = $pieces->where('status', 'Assigned')->count();
      $inServiceCount = $pieces->whereNotIn('status', ['Available', 'Assigned'])->count();
    @endphp
    <div class="lbl-item-summary-panel">
      <div class="lbl-summary-panel-heading">{{ $title }}</div>
      <div class="lbl-item-stats">
        <div class="lbl-item-stat">
          <div class="lbl-item-stat-label">Total Pieces</div>
          <div class="lbl-item-stat-value">{{ $totalLabels }}</div>
        </div>
        <div class="lbl-item-stat">
          <div class="lbl-item-stat-label">Available</div>
          <div class="lbl-item-stat-value" style="color: #3B6D11">{{ $availableCount }}</div>
        </div>
        <div class="lbl-item-stat">
          <div class="lbl-item-stat-label">Assigned</div>
          <div class="lbl-item-stat-value" style="color: #185FA5">{{ $assignedCount }}</div>
        </div>
        <div class="lbl-item-stat">
          <div class="lbl-item-stat-label">In Service</div>
          <div class="lbl-item-stat-value" style="color: #854F0B">{{ $inServiceCount }}</div>
        </div>
      </div>
      @if($assignedCount > 0 || $inServiceCount > 0)
        <div class="lbl-item-note">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><path d="M8 6v4M8 11v1"/></svg>
          {{ $assignedCount + $inServiceCount }} pieces are currently assigned or in service — their labels will still print.
        </div>
      @endif
    </div>
  @endif

  {{-- PREVIEW SECTION --}}
  <div class="lbl-preview-section">
    <div class="lbl-preview-heading">
      <span>Label Preview</span>
      <span class="lbl-preview-heading-note">Showing actual print size</span>
    </div>

    <div class="lbl-a4-container">
      <div class="lbl-a4-sheet">
        <div class="lbl-watermark">PREVIEW</div>
        <div class="lbl-sheet lbl-sheet-{{ $size }}">
          @foreach($pieces->take($labelsPerSheet) as $piece)
            @php $lastThreeDigits = substr($piece->unique_code, -3); @endphp
            <div class="lbl-label lbl-{{ $size }}">
              <div class="lbl-header">
                <div class="lbl-header-line1">{{ strtoupper($piece->item->name) }}</div>
                <div class="lbl-header-line2">{{ $piece->unique_code }}</div>
              </div>
              <div class="lbl-qr-zone">
                {!! $piece->getQrCodeSvg() !!}
              </div>
              <div class="lbl-footer-band">
                <div class="lbl-footer-left">
                  @if(file_exists(public_path('images/grey-apple-events-logo.png')))
                    <img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="" class="lbl-footer-logo">
                  @else
                    <div class="lbl-footer-logo-fallback">G</div>
                  @endif
                </div>
                <div class="lbl-footer-center">GREY APPLE EVENTS</div>
                <div class="lbl-footer-right">{{ $lastThreeDigits }}</div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="lbl-sheet-note">Sheet 1 of {{ $totalSheets }} — showing {{ min($labelsPerSheet, $totalLabels) }} of {{ $totalLabels }} labels</div>
      </div>
    </div>
  </div>
</div>

{{-- PRINT CONTAINER --}}
<div class="lbl-print-container">
  @foreach($pieces->chunk($labelsPerSheet) as $sheetIndex => $sheetPieces)
    <div class="lbl-sheet lbl-sheet-{{ $size }} {{ $sheetIndex > 0 ? 'lbl-page-break' : '' }}">
      @foreach($sheetPieces as $piece)
        @php $lastThreeDigits = substr($piece->unique_code, -3); @endphp
        <div class="lbl-label lbl-{{ $size }}">
          <div class="lbl-header">
            <div class="lbl-header-line1">{{ strtoupper($piece->item->name) }}</div>
            <div class="lbl-header-line2">{{ $piece->unique_code }}</div>
          </div>
          <div class="lbl-qr-zone">
            {!! $piece->getQrCodeSvg() !!}
          </div>
          <div class="lbl-footer-band">
            <div class="lbl-footer-left">
              @if(file_exists(public_path('images/grey-apple-events-logo.png')))
                <img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="" class="lbl-footer-logo">
              @else
                <div class="lbl-footer-logo-fallback">G</div>
              @endif
            </div>
            <div class="lbl-footer-center">GREY APPLE EVENTS</div>
            <div class="lbl-footer-right">{{ $lastThreeDigits }}</div>
          </div>
        </div>
      @endforeach
    </div>
  @endforeach
</div>

<script>
function changeSize(size) {
  const url = new URL(window.location.href);
  url.searchParams.set('size', size);
  window.location.href = url.toString();
}
</script>

</body>
</html>
