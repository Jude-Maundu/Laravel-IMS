@extends('layouts.app')
@section('title', 'Scan Session — ' . $event->name)
@section('page-title', 'Events')

@section('content')

{{-- CUSTOM CONFIRMATION MODAL --}}
<x-confirm-modal />

{{-- DISPATCH WIZARD MODAL --}}
<x-dispatch-wizard-modal :event="$event" :scanSession="$scanSession" :qrCodeSvg="$qrCodeSvg" />

{{-- BREADCRUMB --}}
<div class="wiz-breadcrumb">
  <a href="{{ route('events.show', $event) }}" class="wiz-back-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    {{ $event->name }}
  </a>
  <span class="wiz-bc-sep">/</span>
  <span class="wiz-bc-current">Scan Session</span>
</div>

{{-- FLASH --}}
@if(session('success'))
  <div class="ev-flash ev-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- HEADER --}}
<div class="scan-monitor-header">
  <div>
    <h1 style="font-size: 18px; font-weight: 700; color: #0f0f0f; margin: 0 0 6px 0;">{{ $event->name }}</h1>
    <div style="display: flex; gap: 10px; align-items: center;">
      <span class="disp-ref-badge">{{ $dispatchRef ?? 'DISP-2026-000' }}</span>
      <span class="scan-countdown" id="countdown-timer">--:--:--</span>
    </div>
  </div>
  <div style="display: flex; gap: 10px; align-items: center;">
    <button onclick="showQRModal()" class="evsh-btn-outline">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
      </svg>
      Preview QR
    </button>
    <button onclick="downloadPackingList()" class="evsh-btn-outline" id="download-packing-btn">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
        <polyline points="14 2 14 8 20 8"></polyline>
        <line x1="12" y1="18" x2="12" y2="12"></line>
        <line x1="9" y1="15" x2="15" y2="15"></line>
      </svg>
      <span id="download-text">Download Packing List</span>
    </button>
    <button onclick="extendSession()" class="evsh-btn-outline" id="extend-btn">
      Extend Session (+4hrs)
    </button>
    <form method="POST" action="{{ route('events.scan.cancel', [$event, $scanSession]) }}" style="display:inline" onsubmit="return confirmCancelSession()">
      @csrf
      <button type="submit" class="evsh-btn-outline" style="border-color: #f5c0c0; color: #CC0000;">
        Cancel Session
      </button>
    </form>
  </div>
</div>

{{-- STEP 1: PRINT OR SHARE --}}
<div class="scan-step-card">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
    <div class="scan-step-num">1</div>
    <h2 style="font-size: 14px; font-weight: 700; color: #0f0f0f; margin: 0;">Print or Share</h2>
  </div>
  <div style="display: flex; gap: 12px;">
    <a href="{{ route('events.packing-list.dispatch', [$event, $scanSession]) }}" target="_blank" class="evsh-btn-outline">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="4"/><path d="M2 6v6h12V6"/><path d="M5 10h6"/></svg>
      Print Packing List
    </a>
    <a href="https://wa.me/?text={{ urlencode('Grey Apple IMS — Scan session for ' . $event->name . ' is ready. Open on your phone: ' . config('app.url') . '/scan/' . $scanSession->session_token) }}" target="_blank" class="evsh-btn-outline" style="border-color: #25D366; color: #25D366;">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 100 16 8 8 0 000-16zm.2 11.8c-.5 0-1-.1-1.4-.3l-1.6.5.5-1.5c-.3-.5-.5-1-.5-1.5 0-1.7 1.4-3 3-3s3 1.3 3 3-1.3 3-3 3z"/></svg>
      Share via WhatsApp
    </a>
  </div>
</div>

{{-- STEP 2: SESSION QR --}}
<div class="scan-step-card">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
    <div class="scan-step-num">2</div>
    <h2 style="font-size: 14px; font-weight: 700; color: #0f0f0f; margin: 0;">Session QR Code</h2>
  </div>
  <div class="scan-qr-container">
    <div style="background: #fff; border: 2px solid #ece8e3; border-radius: 12px; padding: 20px; display: inline-block;">
      {!! QrCode::format('svg')->size(220)->errorCorrection('H')->generate(config('app.url') . '/scan/' . $scanSession->session_token) !!}
    </div>
    <p style="font-size: 11px; color: #a09890; margin: 12px 0 0 0; font-family: 'Courier New', monospace;">
      Session Token: {{ $scanSession->session_token }}
    </p>
    <div id="session-status" class="scan-status-indicator" style="margin-top: 12px; padding: 8px 12px; border-radius: 6px; font-size: 12px; @if($scanSession->scanned_count > 0) background: #eaf3de; color: #3B6D11; @else background: #faeeda; color: #854F0B; @endif text-align: center;">
      @if($scanSession->scanned_count > 0)
        Session Active
      @else
        Waiting for first scan...
      @endif
    </div>
  </div>
</div>

{{-- STEP 3: LIVE PROGRESS --}}
<div class="scan-step-card">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
    <div class="scan-step-num">3</div>
    <h2 style="font-size: 14px; font-weight: 700; color: #0f0f0f; margin: 0;">Live Progress</h2>
  </div>
  <div>
    @php
      $totalRequired = $event->eventItems->sum('quantity_requested');
      $currentScanned = $scanSession->scanned_count;
      $initialPercentage = $totalRequired > 0 ? round(($currentScanned / $totalRequired) * 100) : 0;
    @endphp
    <div class="scan-progress-bar-wrapper">
      <div class="scan-progress-bar" id="progress-bar" style="width: {{ $initialPercentage }}%"></div>
    </div>
    <div style="text-align: center; margin-top: 14px;">
      <div style="font-size: 32px; font-weight: 700; color: #0f0f0f;" id="progress-count">{{ $currentScanned }} of {{ $totalRequired }}</div>
      <div style="font-size: 11px; color: #a09890; margin-top: 4px;">items scanned</div>
    </div>
    <div class="scan-recent-list" id="recent-scans" style="margin-top: 20px;">
      @if($currentScanned > 0)
        <p style="font-size: 11px; color: #b0a8a0; text-align: center;">Loading recent scans...</p>
      @else
        <p style="font-size: 11px; color: #b0a8a0; text-align: center;">No items scanned yet.</p>
      @endif
    </div>

    {{-- Per-Item Progress Breakdown --}}
    <div class="scan-item-breakdown" id="item-breakdown" style="margin-top: 24px;">
      <div class="scan-breakdown-title">Item Progress</div>
      <div id="breakdown-list">
        @foreach($event->eventItems as $eventItem)
        @php
          $scannedForItem = \App\Models\ScanSessionPiece::where('scan_session_id', $scanSession->id)
              ->where('item_id', $eventItem->item_id)
              ->count();
          $itemPercentage = $eventItem->quantity_requested > 0
              ? round(($scannedForItem / $eventItem->quantity_requested) * 100)
              : 0;
          $itemComplete = $scannedForItem >= $eventItem->quantity_requested;
        @endphp
        <div class="scan-breakdown-row" id="item-row-{{ $eventItem->item_id }}" @if($itemComplete) style="opacity: 0.6;" @endif>
          <div class="scan-breakdown-name">
            {{ $eventItem->item->name }}
          </div>
          <div class="scan-breakdown-bar-wrap">
            <div class="scan-breakdown-bar"
                 id="item-bar-{{ $eventItem->item_id }}"
                 style="width:{{ $itemPercentage }}%">
            </div>
          </div>
          <div class="scan-breakdown-count"
               id="item-count-{{ $eventItem->item_id }}">
            {{ $scannedForItem }} / {{ $eventItem->quantity_requested }}
          </div>
          <div class="scan-breakdown-status"
               id="item-status-{{ $eventItem->item_id }}"
               @if($itemComplete) style="color: #166534;" @endif>
            @if($itemComplete) ✓ @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- STEP 4: BORROWED & OPERATIONAL ITEMS CONFIRMATION --}}
@if($event->borrowedItems->count() > 0 || $event->operationalItems->count() > 0)
<div class="scan-step-card">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
    <div class="scan-step-num">4</div>
    <h2 style="font-size: 14px; font-weight: 700; color: #0f0f0f; margin: 0;">Confirm Borrowed & Operational Items</h2>
  </div>
  <form id="borrowed-operational-form">
    @csrf

    @if($event->borrowedItems->count() > 0)
    <div style="margin-bottom: 20px;">
      <h3 style="font-size: 13px; font-weight: 600; color: #0f0f0f; margin-bottom: 10px;">Borrowed Items</h3>
      @foreach($event->borrowedItems as $borrowed)
      <div style="background: #fafaf9; border: 1px solid #ece8e3; border-radius: 8px; padding: 12px; margin-bottom: 8px;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
          <input type="checkbox"
                 class="borrowed-checkbox"
                 data-id="{{ $borrowed->id }}"
                 onchange="updateConfirmButton()"
                 style="width: 18px; height: 18px; cursor: pointer;">
          <div style="flex: 1;">
            <div style="font-size: 13px; font-weight: 600; color: #0f0f0f;">{{ $borrowed->item_name }}</div>
            <div style="font-size: 11px; color: #a09890;">{{ $borrowed->source_company }} · Qty: {{ $borrowed->quantity_dispatched }}</div>
          </div>
        </label>
      </div>
      @endforeach
    </div>
    @endif

    @if($event->operationalItems->count() > 0)
    <div>
      <h3 style="font-size: 13px; font-weight: 600; color: #0f0f0f; margin-bottom: 10px;">Operational Items</h3>
      @foreach($event->operationalItems as $opItem)
      <div style="background: #fafaf9; border: 1px solid #ece8e3; border-radius: 8px; padding: 12px; margin-bottom: 8px;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
          <input type="checkbox"
                 class="operational-checkbox"
                 data-id="{{ $opItem->id }}"
                 onchange="updateConfirmButton()"
                 style="width: 18px; height: 18px; cursor: pointer;">
          <div style="flex: 1;">
            <div style="font-size: 13px; font-weight: 600; color: #0f0f0f;">
              {{ $opItem->operationalItem->name ?? $opItem->custom_name }}
            </div>
            <div style="font-size: 11px; color: #a09890;">Qty: {{ $opItem->quantity_dispatched }}</div>
          </div>
        </label>
      </div>
      @endforeach
    </div>
    @endif
  </form>
</div>
@endif

{{-- STEP 5: FINAL DISPATCH CONFIRMATION --}}
<div class="scan-step-card" style="background: linear-gradient(135deg, #fff8f8 0%, #ffffff 100%); border: 2px solid #f5c0c0;">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
    <div class="scan-step-num" style="background: #CC0000; color: #ffffff;">5</div>
    <h2 style="font-size: 14px; font-weight: 700; color: #CC0000; margin: 0;">Final Dispatch Confirmation</h2>
  </div>

  <div id="dispatch-status" style="background: #faeeda; border: 1px solid #f3ddb7; border-radius: 6px; padding: 12px; margin-bottom: 16px; font-size: 12px; color: #854F0B;">
    ⏳ Waiting for all items to be scanned...
  </div>

  <button type="button"
          id="final-confirm-dispatch-btn"
          onclick="confirmAndDispatch()"
          disabled
          style="width: 100%; background: #d0d0d0; color: #ffffff; border: none; border-radius: 8px; padding: 16px 24px; font-size: 16px; font-weight: 700; cursor: not-allowed; transition: all 0.3s;">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="vertical-align: middle; margin-right: 8px;"><path d="M2 13V5l6-3 6 3v8"/><rect x="6" y="9" width="4" height="4"/></svg>
    Confirm & Dispatch All Items
  </button>

  <div id="dispatch-loading" style="display: none; text-align: center; padding: 16px; font-size: 14px; color: #166534;">
    🚀 Dispatching items...
  </div>
</div>

{{-- SWITCH TO MANUAL --}}
<div style="text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #ece8e3;">
  <p style="font-size: 12px; color: #a09890; margin: 0 0 10px 0;">Having trouble scanning?</p>
  <a href="{{ route('events.dispatch.manual', $event) }}" class="evsh-btn-outline" style="border-color: #CC0000; color: #CC0000;">
    Switch to Manual Dispatch
  </a>
</div>

{{-- QR PREVIEW MODAL --}}
<div id="qr-preview-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(4px); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#fff; border-radius:16px; padding:32px; max-width:420px; width:100%; text-align:center; position:relative;">
    <button onclick="closeQRModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; color:#5c5550; cursor:pointer; padding:6px; border-radius:6px; transition:all 0.2s;">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
    <h3 style="font-size:16px; font-weight:700; color:#0f0f0f; margin:0 0 8px 0;">Scan Session QR Code</h3>
    <p style="font-size:12px; color:#5c5550; margin:0 0 20px 0;">{{ $event->name }}</p>
    <div style="background:#fff; border:2px solid #ece8e3; border-radius:12px; padding:20px; display:inline-block; margin-bottom:16px;">
      {!! QrCode::format('svg')->size(240)->errorCorrection('H')->generate(config('app.url') . '/scan/' . $scanSession->session_token) !!}
    </div>
    <p style="font-size:11px; color:#a09890; margin:0; font-family:'Courier New',monospace;">
      Token: {{ $scanSession->session_token }}
    </p>
    <p style="font-size:11px; color:#a09890; margin:8px 0 0 0;">
      Expires: {{ $scanSession->expires_at->format('d M Y, H:i') }} EAT
    </p>
  </div>
</div>

<script>
// Show QR Preview Modal
function showQRModal() {
  document.getElementById('qr-preview-modal').style.display = 'flex';
}

// Close QR Preview Modal
function closeQRModal() {
  document.getElementById('qr-preview-modal').style.display = 'none';
}

// Download Packing List
async function downloadPackingList() {
  const btn = document.getElementById('download-packing-btn');
  const btnText = document.getElementById('download-text');
  const originalText = btnText.textContent;

  btn.disabled = true;
  btnText.textContent = 'Generating...';

  try {
    const response = await fetch("{{ route('events.packing-list.dispatch', [$event, $scanSession]) }}", {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!response.ok) throw new Error('Failed to download');

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'DISPATCH-PACKING-LIST-{{ strtoupper(str_replace(" ", "-", $event->name)) }}.pdf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    btnText.textContent = 'Downloaded!';
    setTimeout(() => {
      btn.disabled = false;
      btnText.textContent = originalText;
    }, 2000);
  } catch (error) {
    console.error('Download error:', error);
    btnText.textContent = 'Failed';
    setTimeout(() => {
      btn.disabled = false;
      btnText.textContent = originalText;
    }, 2000);
  }
}

// Confirm cancel session and clear wizard state
function confirmCancelSession() {
  showConfirmModal({
    title: 'Cancel Scan Session?',
    message: 'You will be returned to the event page.\nYou can start a new dispatch session anytime.',
    confirmText: 'Yes, Cancel Session',
    type: 'warning',
    onConfirm: function() {
      sessionStorage.removeItem('dispatch_wizard_seen_{{ $event->id }}');
      event.target.closest('form').submit();
    }
  });
  return false;
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeQRModal();
  }
});

// Close modal on outside click
document.getElementById('qr-preview-modal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeQRModal();
  }
});

// Countdown timer
let expiresAt = new Date("{{ $scanSession->expires_at->toISOString() }}");

function updateCountdown() {
  const now = new Date();
  const diff = expiresAt - now;

  if (diff <= 0) {
    document.getElementById('countdown-timer').textContent = 'EXPIRED';
    document.getElementById('countdown-timer').style.color = '#CC0000';
    return;
  }

  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  document.getElementById('countdown-timer').textContent =
    String(hours).padStart(2, '0') + ':' +
    String(minutes).padStart(2, '0') + ':' +
    String(seconds).padStart(2, '0');
}

setInterval(updateCountdown, 1000);
updateCountdown();

// Extend session
function extendSession() {
  const btn = document.getElementById('extend-btn');
  btn.disabled = true;
  btn.textContent = 'Extending...';

  fetch("{{ route('events.scan.extend', [$event, $scanSession]) }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      expiresAt = new Date(data.expires_at);
      updateCountdown();
      btn.textContent = 'Extended (+4hrs)';
      setTimeout(() => {
        btn.textContent = 'Extend Session (+4hrs)';
        btn.disabled = false;
      }, 2000);
    }
  })
  .catch(() => {
    btn.textContent = 'Failed';
    btn.disabled = false;
  });
}

// ULTRA-FAST POLLING FOR REAL-TIME SYNC (300ms interval)
let lastScannedCount = {{ $scanSession->scanned_count }};
let pollInterval = null;
let failedAttempts = 0;
const MAX_FAILED_ATTEMPTS = 5;
const TOTAL_REQUIRED = {{ $event->eventItems->sum('quantity_requested') }};

function updateProgress(data) {
  if (!data) return;

  const percentage = data.percentage || 0;
  const progressBar = document.getElementById('progress-bar');
  const progressCount = document.getElementById('progress-count');

  if (progressBar) progressBar.style.width = percentage + '%';
  if (progressCount) progressCount.textContent = data.scanned_count + ' of ' + data.total_pieces;

  // Flash progress bar on new scan + sound
  if (data.scanned_count > lastScannedCount) {
    console.log('🎯 NEW SCAN DETECTED:', lastScannedCount, '→', data.scanned_count);

    if (progressBar) {
      progressBar.style.transition = 'none';
      progressBar.style.background = '#22c55e';
      progressBar.style.boxShadow = '0 0 20px rgba(34, 197, 94, 0.6)';
      setTimeout(() => {
        progressBar.style.transition = 'all 0.5s ease';
        progressBar.style.background = '#CC0000';
        progressBar.style.boxShadow = 'none';
      }, 300);
    }

    lastScannedCount = data.scanned_count;

    // Play success sound
    try {
      const audio = new Audio("{{ asset('sounds/success.mp3') }}");
      audio.volume = 0.4;
      audio.play().catch(e => {});
    } catch(e) {}
  }

  if (data.scanned_count > 0) {
    const sessionStatus = document.getElementById('session-status');
    if (sessionStatus) {
      sessionStatus.textContent = 'Session Active — Live Updates Every 300ms';
      sessionStatus.style.background = '#eaf3de';
      sessionStatus.style.color = '#3B6D11';
      sessionStatus.style.fontWeight = '600';
    }
  }

  // Update per-item progress breakdown
  if (data.item_progress) {
    data.item_progress.forEach(function(item) {
      var countEl  = document.getElementById('item-count-' + item.item_id);
      var barEl    = document.getElementById('item-bar-'   + item.item_id);
      var statusEl = document.getElementById('item-status-' + item.item_id);
      var rowEl    = document.getElementById('item-row-'   + item.item_id);

      if (countEl) {
        const oldText = countEl.textContent;
        const newText = item.scanned + ' / ' + item.required;

        if (oldText !== newText) {
          countEl.textContent = newText;
          // Flash animation on count change
          countEl.style.background = '#eaf3de';
          countEl.style.color = '#166534';
          countEl.style.fontWeight = '700';
          setTimeout(() => {
            countEl.style.background = '';
            countEl.style.color = '';
            countEl.style.fontWeight = '';
          }, 500);
        }
      }

      if (barEl) {
        var pct = item.required > 0 ? (item.scanned / item.required) * 100 : 0;
        barEl.style.width = pct + '%';
      }

      if (statusEl) {
        if (item.complete && !statusEl.textContent) {
          // Just completed - animate checkmark
          statusEl.innerHTML = '<span style="font-size: 18px; animation: bounce 0.5s;">✓</span>';
          statusEl.style.color = '#22c55e';
        } else if (item.complete) {
          statusEl.textContent = '✓';
          statusEl.style.color = '#22c55e';
        }
      }

      if (rowEl && item.complete) {
        rowEl.style.opacity = '0.6';
      }
    });
  }

  // Check if all complete
  if (data.all_complete) {
    const sessionStatus = document.getElementById('session-status');
    if (sessionStatus && !sessionStatus.textContent.includes('All Items')) {
      sessionStatus.textContent = '🎉 All Items Scanned!';
      sessionStatus.style.background = '#22c55e';
      sessionStatus.style.color = '#ffffff';
      sessionStatus.style.fontWeight = '700';
    }

    // Update dispatch button state
    updateConfirmButton();
  }

  // Check if session completed
  if (data.session_status === 'completed') {
    clearInterval(pollInterval);
    setTimeout(() => {
      alert('Dispatch session completed! Redirecting...');
      window.location.reload();
    }, 1000);
  }

  failedAttempts = 0; // Reset on success
}

function pollProgress() {
  fetch("{{ route('api.scan.progress', [$event, $scanSession]) }}", {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Cache-Control': 'no-cache',
      'X-Requested-With': 'XMLHttpRequest'
    },
    cache: 'no-store'
  })
  .then(res => {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(data => {
    updateProgress(data);
  })
  .catch(err => {
    failedAttempts++;
    console.error('Poll error (' + failedAttempts + '/' + MAX_FAILED_ATTEMPTS + '):', err.message);

    if (failedAttempts >= MAX_FAILED_ATTEMPTS) {
      clearInterval(pollInterval);
      const sessionStatus = document.getElementById('session-status');
      if (sessionStatus) {
        sessionStatus.textContent = '⚠️ Connection Lost - Please Refresh';
        sessionStatus.style.background = '#fef3c7';
        sessionStatus.style.color = '#854F0B';
      }
    }
  });
}

// Add bounce animation for checkmarks
const style = document.createElement('style');
style.textContent = `
  @keyframes bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.3); }
  }
`;
document.head.appendChild(style);

// Start ultra-fast polling (300ms = 0.3 seconds)
console.log('🚀 Starting real-time polling every 300ms...');
pollInterval = setInterval(pollProgress, 300);
pollProgress(); // Initial call

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
  if (pollInterval) {
    clearInterval(pollInterval);
  }
});

// ── BORROWED & OPERATIONAL CONFIRMATION ────────────────────────
function updateConfirmButton() {
  const allScanned = lastScannedCount >= TOTAL_REQUIRED;

  const borrowedCheckboxes = document.querySelectorAll('.borrowed-checkbox');
  const operationalCheckboxes = document.querySelectorAll('.operational-checkbox');

  const allBorrowedChecked = Array.from(borrowedCheckboxes).every(cb => cb.checked);
  const allOperationalChecked = Array.from(operationalCheckboxes).every(cb => cb.checked);

  const hasBorrowed = borrowedCheckboxes.length > 0;
  const hasOperational = operationalCheckboxes.length > 0;

  const borrowedOk = !hasBorrowed || allBorrowedChecked;
  const operationalOk = !hasOperational || allOperationalChecked;

  const btn = document.getElementById('final-confirm-dispatch-btn');
  const statusDiv = document.getElementById('dispatch-status');

  if (!btn || !statusDiv) return;

  // Update status message
  if (!allScanned) {
    statusDiv.innerHTML = '⏳ Waiting for all items to be scanned...';
    statusDiv.style.background = '#faeeda';
    statusDiv.style.borderColor = '#f3ddb7';
    statusDiv.style.color = '#854F0B';
  } else if (!borrowedOk) {
    statusDiv.innerHTML = '📋 Please confirm all borrowed items are loaded';
    statusDiv.style.background = '#fef3c7';
    statusDiv.style.borderColor = '#fde68a';
    statusDiv.style.color = '#854F0B';
  } else if (!operationalOk) {
    statusDiv.innerHTML = '📋 Please confirm all operational items are loaded';
    statusDiv.style.background = '#fef3c7';
    statusDiv.style.borderColor = '#fde68a';
    statusDiv.style.color = '#854F0B';
  } else {
    statusDiv.innerHTML = '✅ All items ready! Click below to dispatch';
    statusDiv.style.background = '#eaf3de';
    statusDiv.style.borderColor = '#d1e7b8';
    statusDiv.style.color = '#3B6D11';
  }

  // Enable button only when all conditions met
  if (allScanned && borrowedOk && operationalOk) {
    btn.disabled = false;
    btn.style.background = '#CC0000';
    btn.style.cursor = 'pointer';
    btn.style.boxShadow = '0 4px 12px rgba(204, 0, 0, 0.3)';
  } else {
    btn.disabled = true;
    btn.style.background = '#d0d0d0';
    btn.style.cursor = 'not-allowed';
    btn.style.boxShadow = 'none';
  }
}

// ── CONFIRM AND DISPATCH ────────────────────────────────────────
async function confirmAndDispatch() {
  showConfirmModal({
    title: 'Confirm Complete Dispatch?',
    message: 'All scanned items will be dispatched to the venue.\nEvent status will be set to Active.\n\nThis action cannot be undone.',
    confirmText: 'Confirm & Dispatch',
    type: 'success',
    onConfirm: async function() {
      const btn = document.getElementById('final-confirm-dispatch-btn');
      const loadingDiv = document.getElementById('dispatch-loading');

      // Disable button and show loading
      btn.style.display = 'none';
      loadingDiv.style.display = 'block';

      try {
        const response = await fetch("{{ route('events.scan.confirm-dispatch', [$event, $scanSession]) }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        borrowed_confirmed: Array.from(document.querySelectorAll('.borrowed-checkbox')).map(cb => ({
          id: cb.dataset.id,
          checked: cb.checked
        })),
        operational_confirmed: Array.from(document.querySelectorAll('.operational-checkbox')).map(cb => ({
          id: cb.dataset.id,
          checked: cb.checked
        }))
      })
    });

    if (!response.ok) {
      throw new Error('Dispatch failed with status: ' + response.status);
    }

    const data = await response.json();

    if (data.success) {
      // Show success and redirect
      loadingDiv.innerHTML = '✅ Dispatch completed successfully! Redirecting...';
      loadingDiv.style.color = '#166534';

      // Play success sound
      try {
        const audio = new Audio("{{ asset('sounds/success.mp3') }}");
        audio.volume = 0.6;
        audio.play().catch(e => {});
      } catch(e) {}

      setTimeout(() => {
        window.location.href = "{{ route('events.show', $event) }}";
      }, 1500);
    } else {
      throw new Error(data.message || 'Dispatch failed');
    }
  } catch (error) {
    console.error('Dispatch error:', error);
    alert('Error: ' + error.message + '\n\nPlease try again or contact support.');
    btn.style.display = 'block';
    loadingDiv.style.display = 'none';
      }
    }
  });
}

// Initialize button state on page load
document.addEventListener('DOMContentLoaded', function() {
  updateConfirmButton();
});
</script>

@endsection
