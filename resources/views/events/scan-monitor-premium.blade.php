@extends('layouts.app')
@section('title', 'Dispatch Monitor — ' . $event->name)
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
  <span class="wiz-bc-current">Dispatch Monitor</span>
</div>

{{-- FLASH MESSAGES --}}
@if(session('success'))
  <div class="ev-flash ev-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- HEADER --}}
<div class="dm-header">
  <div class="dm-header-left">
    <h1 class="dm-title">Dispatch Monitor</h1>
    <div class="dm-meta">
      <span class="dm-ref">{{ $dispatchRef ?? 'DISP-' . now()->format('Y-m') . '-000' }}</span>
      <span class="dm-sep">•</span>
      <span class="dm-venue">{{ $event->venue }}</span>
      <span class="dm-sep">•</span>
      <span class="dm-countdown" id="countdown-timer">--:--:--</span>
    </div>
  </div>
  <div class="dm-header-right">
    <button type="button" onclick="showQRModal()" class="dm-btn-outline">
      📱 Preview QR
    </button>
    <button type="button" onclick="downloadPackingList()" class="dm-btn-outline" id="download-packing-btn">
      <span id="download-text">📄 Download Packing List</span>
    </button>
    <button type="button" onclick="extendSession()" class="dm-btn-outline" id="extend-btn">
      ⏱️ Extend +4hrs
    </button>
    <form method="POST" action="{{ route('events.scan.cancel', [$event, $scanSession]) }}" style="display:inline;" onsubmit="return confirmCancelSession()">
      @csrf
      <button type="submit" class="dm-btn-danger">
        ✕ Cancel Session
      </button>
    </form>
  </div>
</div>

{{-- OVERALL PROGRESS --}}
<div class="dm-progress-card">
  <div class="dm-progress-label">
    <span id="progress-text">0 / {{ $event->eventItems->sum('quantity_requested') }} items scanned</span>
    <span class="dm-progress-pct" id="progress-pct">0%</span>
  </div>
  <div class="dm-progress-bar-wrapper">
    <div class="dm-progress-bar" id="overall-progress-bar" style="width: 0%"></div>
  </div>
</div>

{{-- GREY APPLE INVENTORY GRID --}}
<div class="dm-section">
  <h2 class="dm-section-title">
    Grey Apple Inventory
    <span class="dm-section-badge" id="items-complete-badge">0 / {{ $event->eventItems->count() }} items complete</span>
  </h2>

  <div class="dm-items-grid" id="items-grid">
    @foreach($event->eventItems as $eventItem)
      @php
        $item = $eventItem->item;
        $imagePath = $item->primaryImage?->image_path ?? $item->image_path;
      @endphp
      <div class="dm-item-card" id="item-card-{{ $item->id }}" data-item-id="{{ $item->id }}">

        {{-- Item Image --}}
        <div class="dm-item-img-wrap">
          @if($imagePath)
            <img src="{{ asset('storage/' . $imagePath) }}" class="dm-item-img" alt="{{ $item->name }}">
          @else
            <div class="dm-item-img-placeholder">📦</div>
          @endif
          <div class="dm-item-status-badge" id="status-badge-{{ $item->id }}">
            <span id="status-icon-{{ $item->id }}">⏳</span>
          </div>
        </div>

        {{-- Item Info --}}
        <div class="dm-item-info">
          <div class="dm-item-name" title="{{ $item->name }}">{{ Str::limit($item->name, 30) }}</div>
          <div class="dm-item-code">{{ strtoupper(substr($item->category ?? 'ITM', 0, 3)) }}-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</div>
        </div>

        {{-- Progress --}}
        <div class="dm-item-progress">
          <div class="dm-item-count" id="count-{{ $item->id }}">
            <span id="scanned-{{ $item->id }}">0</span> / {{ $eventItem->quantity_requested }}
          </div>
          <div class="dm-item-bar-wrap">
            <div class="dm-item-bar" id="bar-{{ $item->id }}" style="width: 0%"></div>
          </div>
        </div>

        {{-- Scanner Attribution --}}
        <div class="dm-item-scanner" id="scanner-{{ $item->id }}">
          <span class="dm-scanner-label">Scanned by:</span>
          <span class="dm-scanner-name" id="scanner-name-{{ $item->id }}">—</span>
        </div>

      </div>
    @endforeach
  </div>
</div>

{{-- BORROWED ITEMS SECTION --}}
@if($event->borrowedItems->count() > 0)
<div class="dm-section">
  <h2 class="dm-section-title">
    Borrowed Items
    <span class="dm-section-note">(Manual Verification - No Scanning)</span>
  </h2>

  <div class="dm-borrowed-list">
    @foreach($event->borrowedItems as $borrowed)
      <div class="dm-borrowed-row" id="borrowed-row-{{ $borrowed->id }}">
        <input type="checkbox"
               id="borrowed-check-{{ $borrowed->id }}"
               class="dm-checkbox borrowed-checkbox"
               data-id="{{ $borrowed->id }}"
               onchange="toggleBorrowedCheck({{ $borrowed->id }})">
        <div class="dm-borrowed-info">
          <div class="dm-borrowed-name">{{ $borrowed->item_name }}</div>
          <div class="dm-borrowed-source">{{ $borrowed->source_company }}</div>
        </div>
        <div class="dm-borrowed-qty">
          <span class="dm-qty-label">Qty:</span>
          <span class="dm-qty-value">{{ $borrowed->quantity_dispatched }}</span>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endif

{{-- OPERATIONAL ITEMS SECTION --}}
@if($event->operationalItems->count() > 0)
<div class="dm-section">
  <h2 class="dm-section-title">
    Operational Items
    <span class="dm-section-note">(Manual Verification - No Scanning)</span>
  </h2>

  <div class="dm-operational-list">
    @foreach($event->operationalItems as $opItem)
      <div class="dm-operational-row" id="operational-row-{{ $opItem->id }}">
        <input type="checkbox"
               id="operational-check-{{ $opItem->id }}"
               class="dm-checkbox operational-checkbox"
               data-id="{{ $opItem->id }}"
               onchange="toggleOperationalCheck({{ $opItem->id }})">
        <div class="dm-operational-info">
          <div class="dm-operational-name">{{ $opItem->operationalItem->name ?? $opItem->custom_name }}</div>
        </div>
        <div class="dm-operational-qty">
          <span class="dm-qty-label">Qty:</span>
          <span class="dm-qty-value">{{ $opItem->quantity_dispatched }}</span>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endif

{{-- RECENT ACTIVITY FEED --}}
<div class="dm-section">
  <h2 class="dm-section-title">Recent Scans</h2>
  <div class="dm-activity-feed" id="activity-feed">
    <p class="dm-empty-state">No scans yet. Waiting for first scan...</p>
  </div>
</div>

{{-- FOOTER ACTIONS --}}
<div class="dm-footer">
  <button type="button" onclick="undoLastScan()" class="dm-btn-outline" id="undo-btn" disabled>
    ↶ Undo Last Scan
  </button>
  <button type="button" onclick="confirmDispatch()" class="dm-btn-primary" id="confirm-btn" disabled>
    ✓ Confirm Complete Dispatch
  </button>
</div>

{{-- JAVASCRIPT --}}
<script>
// ═══════════════════════════════════════════════════════════════
// DISPATCH MONITOR JAVASCRIPT
// ═══════════════════════════════════════════════════════════════

const EVENT_ID = {{ $event->id }};
const SESSION_ID = {{ $scanSession->id }};
const TOTAL_REQUIRED = {{ $event->eventItems->sum('quantity_requested') }};
const ITEM_COUNT = {{ $event->eventItems->count() }};
const BORROWED_COUNT = {{ $event->borrowedItems->count() }};
const OPERATIONAL_COUNT = {{ $event->operationalItems->count() }};

let expiresAt = new Date('{{ $scanSession->expires_at->toISOString() }}');
let borrowedChecked = new Set();
let operationalChecked = new Set();
let pollInterval = null;

// ─── CONFIRM CANCEL SESSION ─────────────────────────────────────
function confirmCancelSession() {
  showConfirmModal({
    title: 'Cancel Scan Session?',
    message: 'You will be returned to the event page.\nYou can start a new dispatch session anytime.',
    confirmText: 'Yes, Cancel Session',
    type: 'warning',
    onConfirm: function() {
      sessionStorage.removeItem('dispatch_wizard_seen_{{ $event->id }}');
      // Submit the form
      event.target.closest('form').submit();
    }
  });
  return false; // Prevent default form submission
}

// ─── COUNTDOWN TIMER ─────────────────────────────────────────────
function updateCountdown() {
  const now = new Date();
  const diff = expiresAt - now;

  if (diff <= 0) {
    document.getElementById('countdown-timer').textContent = 'EXPIRED';
    document.getElementById('countdown-timer').classList.add('expiring');
    return;
  }

  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  const timeStr = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
  document.getElementById('countdown-timer').textContent = timeStr;

  // Warning when < 15 minutes
  if (diff < 15 * 60 * 1000) {
    document.getElementById('countdown-timer').classList.add('expiring');
  }
}

setInterval(updateCountdown, 1000);
updateCountdown();

// ─── EXTEND SESSION ──────────────────────────────────────────────
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
      btn.textContent = '✓ Extended';
      setTimeout(() => {
        btn.textContent = '⏱️ Extend +4hrs';
        btn.disabled = false;
      }, 2000);
    }
  })
  .catch(err => {
    console.error('Extend failed:', err);
    btn.textContent = '✕ Failed';
    btn.disabled = false;
  });
}

// ─── POLL PROGRESS ───────────────────────────────────────────────
function pollProgress() {
  fetch("{{ route('api.scan.progress', [$event, $scanSession]) }}")
    .then(res => res.json())
    .then(data => {
      // Update overall progress
      const pct = data.percentage || 0;
      document.getElementById('overall-progress-bar').style.width = pct + '%';
      document.getElementById('progress-text').textContent =
        `${data.scanned_count} / ${data.total_pieces} items scanned`;
      document.getElementById('progress-pct').textContent = pct + '%';

      // Update per-item progress
      if (data.item_progress) {
        let completedCount = 0;
        data.item_progress.forEach(item => {
          const scannedSpan = document.getElementById('scanned-' + item.item_id);
          const bar = document.getElementById('bar-' + item.item_id);
          const statusIcon = document.getElementById('status-icon-' + item.item_id);
          const card = document.getElementById('item-card-' + item.item_id);
          const scannerName = document.getElementById('scanner-name-' + item.item_id);

          if (scannedSpan) scannedSpan.textContent = item.scanned;

          if (bar) {
            const itemPct = item.required > 0 ? (item.scanned / item.required) * 100 : 0;
            bar.style.width = itemPct + '%';
          }

          if (item.complete) {
            completedCount++;
            if (statusIcon) statusIcon.textContent = '✓';
            if (card) card.classList.add('completed');
          } else {
            if (statusIcon) statusIcon.textContent = '⏳';
            if (card) card.classList.remove('completed');
          }

          // Show scanner name if available
          if (scannerName && item.scanner_name) {
            scannerName.textContent = item.scanner_name;
          }
        });

        // Update items complete badge
        document.getElementById('items-complete-badge').textContent =
          `${completedCount} / ${ITEM_COUNT} items complete`;
      }

      // Update recent activity
      if (data.recent_scans && data.recent_scans.length > 0) {
        const feedHtml = data.recent_scans.map(scan => `
          <div class="dm-activity-item">
            <div class="dm-activity-time">${scan.scanned_at}</div>
            <div class="dm-activity-desc">
              <span class="dm-activity-user">${scan.scanner_name || 'Unknown'}</span> scanned
              <span class="dm-activity-item-name">${scan.unique_code}</span>
              (${scan.item_name})
            </div>
          </div>
        `).join('');
        document.getElementById('activity-feed').innerHTML = feedHtml;

        // Enable undo button if there are scans
        document.getElementById('undo-btn').disabled = false;
      }

      // Always check dispatch ready state after updating progress
      checkDispatchReady();
    })
    .catch(err => console.error('Poll failed:', err));
}

// Poll every 5 seconds
pollInterval = setInterval(pollProgress, 5000);
pollProgress();

// ─── BORROWED/OPERATIONAL CHECKBOXES ─────────────────────────────
function toggleBorrowedCheck(id) {
  const checkbox = document.getElementById('borrowed-check-' + id);
  const row = document.getElementById('borrowed-row-' + id);

  if (checkbox.checked) {
    borrowedChecked.add(id);
    row.classList.add('checked');
  } else {
    borrowedChecked.delete(id);
    row.classList.remove('checked');
  }

  console.log('Borrowed checked:', Array.from(borrowedChecked), 'Total:', borrowedChecked.size, 'Required:', BORROWED_COUNT);
  checkDispatchReady();
}

function toggleOperationalCheck(id) {
  const checkbox = document.getElementById('operational-check-' + id);
  const row = document.getElementById('operational-row-' + id);

  if (checkbox.checked) {
    operationalChecked.add(id);
    row.classList.add('checked');
  } else {
    operationalChecked.delete(id);
    row.classList.remove('checked');
  }

  console.log('Operational checked:', Array.from(operationalChecked), 'Total:', operationalChecked.size, 'Required:', OPERATIONAL_COUNT);
  checkDispatchReady();
}

// ─── CHECK IF DISPATCH READY ─────────────────────────────────────
function checkDispatchReady() {
  // Check if all borrowed items verified (if any exist)
  const borrowedReady = BORROWED_COUNT === 0 || borrowedChecked.size === BORROWED_COUNT;

  // Check if all operational items verified (if any exist)
  const operationalReady = OPERATIONAL_COUNT === 0 || operationalChecked.size === OPERATIONAL_COUNT;

  // Check if all items scanned (get current count from progress text)
  const progressText = document.getElementById('progress-text').textContent;
  const match = progressText.match(/(\d+)\s*\/\s*(\d+)/);
  const scannedCount = match ? parseInt(match[1]) : 0;
  const totalCount = match ? parseInt(match[2]) : TOTAL_REQUIRED;
  const allScanned = scannedCount >= totalCount;

  console.log('Dispatch ready check:', {
    allScanned,
    scannedCount,
    totalCount,
    borrowedReady,
    borrowedChecked: borrowedChecked.size,
    borrowedRequired: BORROWED_COUNT,
    operationalReady,
    operationalChecked: operationalChecked.size,
    operationalRequired: OPERATIONAL_COUNT
  });

  // Enable confirm button ONLY when ALL conditions met
  const confirmBtn = document.getElementById('confirm-btn');
  if (allScanned && borrowedReady && operationalReady) {
    console.log('✅ ALL CONDITIONS MET - Enabling button');
    confirmBtn.disabled = false;
    confirmBtn.classList.add('enabled');
    confirmBtn.style.background = '#CC0000';
    confirmBtn.style.cursor = 'pointer';
  } else {
    console.log('❌ NOT READY:', {allScanned, borrowedReady, operationalReady});
    confirmBtn.disabled = true;
    confirmBtn.classList.remove('enabled');
    confirmBtn.style.background = '#d0d0d0';
    confirmBtn.style.cursor = 'not-allowed';
  }
}

// ─── UNDO LAST SCAN ──────────────────────────────────────────────
function undoLastScan() {
  showConfirmModal({
    title: 'Undo Last Scan?',
    message: 'This will remove the most recently scanned item.\nThis action cannot be undone.',
    confirmText: 'Yes, Undo',
    type: 'warning',
    onConfirm: function() {
      const btn = document.getElementById('undo-btn');
      btn.disabled = true;
      btn.textContent = 'Undoing...';

      fetch("{{ route('events.scan.undo', [$event, $scanSession]) }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      btn.textContent = '✓ Undone';
      pollProgress(); // Refresh display
      setTimeout(() => {
        btn.textContent = '↶ Undo Last Scan';
        btn.disabled = false;
      }, 1500);
    } else {
      alert(data.message || 'Failed to undo scan');
      btn.textContent = '↶ Undo Last Scan';
      btn.disabled = false;
    }
  })
  .catch(err => {
    console.error('Undo failed:', err);
    alert('Failed to undo scan');
    btn.textContent = '↶ Undo Last Scan';
    btn.disabled = false;
      });
    }
  });
}

// ─── CONFIRM DISPATCH ────────────────────────────────────────────
function confirmDispatch() {
  showConfirmModal({
    title: 'Confirm Complete Dispatch?',
    message: 'All scanned items will be dispatched to the venue.\nEvent status will be set to Active.\n\nThis action cannot be undone.',
    confirmText: 'Confirm & Dispatch',
    type: 'success',
    onConfirm: function() {
      const btn = document.getElementById('confirm-btn');
      btn.disabled = true;
      btn.textContent = 'Dispatching...';

      fetch("{{ route('events.scan.confirm-dispatch', [$event, $scanSession]) }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          borrowed_confirmed: Array.from(borrowedChecked),
          operational_confirmed: Array.from(operationalChecked)
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Stop polling immediately
          clearInterval(pollInterval);

          btn.textContent = '✓ Dispatched!';
          btn.style.background = '#166534';

          // Play success sound
          try {
            const audio = new Audio("{{ asset('sounds/success.mp3') }}");
            audio.volume = 0.6;
            audio.play().catch(e => {});
          } catch(e) {}

          // Redirect after short delay
          setTimeout(() => {
            window.location.href = "{{ route('events.show', $event) }}";
          }, 1000);
        } else {
          alert(data.message || 'Failed to dispatch');
          btn.textContent = '✓ Confirm Complete Dispatch';
          btn.disabled = false;
          btn.style.background = '#CC0000';
        }
      })
      .catch(err => {
        console.error('Dispatch error:', err);
        alert('Network error. Please check your connection and try again.');
        btn.textContent = '✓ Confirm Complete Dispatch';
        btn.disabled = false;
        btn.style.background = '#CC0000';
      });
    }
  });
}

// ─── QR PREVIEW MODAL ────────────────────────────────────────────
function showQRModal() {
  document.getElementById('qr-preview-modal').style.display = 'flex';
}

function closeQRModal() {
  document.getElementById('qr-preview-modal').style.display = 'none';
}

// ─── DOWNLOAD PACKING LIST ───────────────────────────────────────
async function downloadPackingList() {
  const btn = document.getElementById('download-packing-btn');
  const btnText = document.getElementById('download-text');
  const originalText = btnText.textContent;

  btn.disabled = true;
  btnText.textContent = '⏳ Generating...';

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

    btnText.textContent = '✓ Downloaded!';
    setTimeout(() => {
      btn.disabled = false;
      btnText.textContent = originalText;
    }, 2000);
  } catch (error) {
    console.error('Download error:', error);
    btnText.textContent = '✗ Failed';
    setTimeout(() => {
      btn.disabled = false;
      btnText.textContent = originalText;
    }, 2000);
  }
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeQRModal();
  }
});
</script>

{{-- QR PREVIEW MODAL --}}
<div id="qr-preview-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(4px); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px;">
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

@endsection
