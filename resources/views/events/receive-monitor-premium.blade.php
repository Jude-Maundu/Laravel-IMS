@extends('layouts.app')
@section('title', 'Receive Monitor — ' . $event->name)
@section('page-title', 'Events')

@section('content')

{{-- CUSTOM CONFIRMATION MODAL --}}
<x-confirm-modal />

{{-- RECEIVE WIZARD MODAL --}}
<x-receive-wizard-modal :event="$event" :receiveSession="$receiveSession" :qrCodeSvg="$qrCodeSvg" />

{{-- BREADCRUMB --}}
<div class="wiz-breadcrumb">
  <a href="{{ route('events.show', $event) }}" class="wiz-back-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    {{ $event->name }}
  </a>
  <span class="wiz-bc-sep">/</span>
  <span class="wiz-bc-current">Receive Monitor</span>
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
    <h1 class="dm-title">Receive Monitor</h1>
    <div class="dm-meta">
      <span class="dm-ref">{{ $receiveRef ?? 'RCV-' . now()->format('Y-m') . '-000' }}</span>
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
    <button type="button" onclick="downloadReceiveNote()" class="dm-btn-outline" id="download-note-btn">
      <span id="download-text">📄 Download Receive Note</span>
    </button>
    <button type="button" onclick="extendSession()" class="dm-btn-outline" id="extend-btn">
      ⏱️ Extend +4hrs
    </button>
    <form method="POST" action="{{ route('events.receive.cancel', [$event, $receiveSession]) }}" style="display:inline;" onsubmit="return confirmCancelSession()">
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
    <span id="progress-text">0 / {{ $totalDispatched }} items received</span>
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
    <span class="dm-section-badge" id="items-complete-badge">0 / {{ $itemProgress->count() }} items complete</span>
  </h2>

  <div class="dm-items-grid" id="items-grid">
    @foreach($itemProgress as $item)
      <div class="dm-item-card" id="item-card-{{ $item['item_id'] }}" data-item-id="{{ $item['item_id'] }}">

        {{-- Item Image --}}
        <div class="dm-item-img-wrap">
          @if($item['image_path'])
            <img src="{{ asset('storage/' . $item['image_path']) }}" class="dm-item-img" alt="{{ $item['item_name'] }}">
          @else
            <div class="dm-item-img-placeholder">📦</div>
          @endif
          <div class="dm-item-status-badge" id="status-badge-{{ $item['item_id'] }}">
            <span id="status-icon-{{ $item['item_id'] }}">⏳</span>
          </div>
        </div>

        {{-- Item Info --}}
        <div class="dm-item-info">
          <div class="dm-item-name" title="{{ $item['item_name'] }}">{{ Str::limit($item['item_name'], 30) }}</div>
          <div class="dm-item-code">{{ strtoupper(substr($item['category'] ?? 'ITM', 0, 3)) }}-{{ str_pad($item['item_id'], 3, '0', STR_PAD_LEFT) }}</div>
        </div>

        {{-- Progress --}}
        <div class="dm-item-progress">
          <div class="dm-item-count" id="count-{{ $item['item_id'] }}">
            <span id="received-{{ $item['item_id'] }}">{{ $item['received'] }}</span> / {{ $item['dispatched'] }}
          </div>
          <div class="dm-item-bar-wrap">
            <div class="dm-item-bar" id="bar-{{ $item['item_id'] }}" style="width: {{ $item['dispatched'] > 0 ? round(($item['received'] / $item['dispatched']) * 100) : 0 }}%"></div>
          </div>
        </div>

        {{-- Scanner Attribution --}}
        <div class="dm-item-scanner" id="scanner-{{ $item['item_id'] }}">
          <span class="dm-scanner-label">Received by:</span>
          <span class="dm-scanner-name" id="scanner-name-{{ $item['item_id'] }}">—</span>
        </div>

      </div>
    @endforeach
  </div>
</div>

{{-- BORROWED ITEMS SECTION --}}
@if($event->borrowedItems->where('quantity_dispatched', '>', 0)->count() > 0)
<div class="dm-section">
  <h2 class="dm-section-title">
    Borrowed Items
    <span class="dm-section-note">(Manual Verification - No Scanning)</span>
  </h2>

  <div class="dm-borrowed-list">
    @foreach($event->borrowedItems->where('quantity_dispatched', '>', 0) as $borrowed)
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
@if($event->operationalItems->where('quantity_dispatched', '>', 0)->count() > 0)
<div class="dm-section">
  <h2 class="dm-section-title">
    Operational Items
    <span class="dm-section-note">(Manual Verification - No Scanning)</span>
  </h2>

  <div class="dm-operational-list">
    @foreach($event->operationalItems->where('quantity_dispatched', '>', 0) as $opItem)
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
  <h2 class="dm-section-title">Recent Receives</h2>
  <div class="dm-activity-feed" id="activity-feed">
    <p class="dm-empty-state">No receives yet. Waiting for first scan...</p>
  </div>
</div>

{{-- FOOTER ACTIONS --}}
<div class="dm-footer">
  <button type="button" onclick="undoLastReceive()" class="dm-btn-outline" id="undo-btn" disabled>
    ↶ Undo Last Receive
  </button>
  <button type="button" onclick="confirmReceive()" class="dm-btn-primary" id="confirm-btn" disabled>
    ✓ Confirm Complete Receive
  </button>
</div>

{{-- JAVASCRIPT --}}
<script>
// ═══════════════════════════════════════════════════════════════
// RECEIVE MONITOR JAVASCRIPT
// ═══════════════════════════════════════════════════════════════

const EVENT_ID = {{ $event->id }};
const SESSION_ID = {{ $receiveSession->id }};
const TOTAL_REQUIRED = {{ $totalDispatched }};
const ITEM_COUNT = {{ $itemProgress->count() }};
const BORROWED_COUNT = {{ $event->borrowedItems->where('quantity_dispatched', '>', 0)->count() }};
const OPERATIONAL_COUNT = {{ $event->operationalItems->where('quantity_dispatched', '>', 0)->count() }};

let expiresAt = new Date('{{ $receiveSession->expires_at->toISOString() }}');
let borrowedChecked = new Set();
let operationalChecked = new Set();
let pollInterval = null;
let lastReceivedCount = {{ $receiveSession->received_count }};
let failedAttempts = 0;
const MAX_FAILED_ATTEMPTS = 5;

// ─── CONFIRM CANCEL SESSION ─────────────────────────────────────
function confirmCancelSession() {
  showConfirmModal({
    title: 'Cancel Receive Session?',
    message: 'You will be returned to the event page.\nYou can start a new receive session anytime.',
    confirmText: 'Yes, Cancel Session',
    type: 'warning',
    onConfirm: function() {
      sessionStorage.removeItem('receive_wizard_seen_{{ $event->id }}');
      event.target.closest('form').submit();
    }
  });
  return false;
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

  fetch("{{ route('events.receive.extend', [$event, $receiveSession]) }}", {
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

// ─── POLL PROGRESS (ULTRA-FAST 300ms POLLING) ───────────────────
function pollProgress() {
  fetch("{{ route('events.receive.progress', [$event, $receiveSession]) }}", {
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
      // Update overall progress
      const pct = data.percentage || 0;
      const progressBar = document.getElementById('overall-progress-bar');
      progressBar.style.width = pct + '%';
      document.getElementById('progress-text').textContent =
        `${data.received_count} / ${data.total_dispatched} items received`;
      document.getElementById('progress-pct').textContent = pct + '%';

      // Flash progress bar on new receive + sound
      if (data.received_count > lastReceivedCount) {
        // Visual flash
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

        lastReceivedCount = data.received_count;

        // Play success sound
        try {
          const audio = new Audio("{{ asset('sounds/success.mp3') }}");
          audio.volume = 0.4;
          audio.play().catch(e => {});
        } catch(e) {}
      }

      // Update per-item progress
      if (data.item_progress) {
        let completedCount = 0;
        data.item_progress.forEach(item => {
          const receivedSpan = document.getElementById('received-' + item.item_id);
          const bar = document.getElementById('bar-' + item.item_id);
          const statusIcon = document.getElementById('status-icon-' + item.item_id);
          const card = document.getElementById('item-card-' + item.item_id);
          const scannerName = document.getElementById('scanner-name-' + item.item_id);

          if (receivedSpan) {
            const oldText = receivedSpan.textContent;
            const newText = item.received;
            if (oldText !== newText.toString()) {
              receivedSpan.textContent = newText;
              // Flash animation on count change
              receivedSpan.style.background = '#eaf3de';
              receivedSpan.style.color = '#166534';
              receivedSpan.style.fontWeight = '700';
              setTimeout(() => {
                receivedSpan.style.background = '';
                receivedSpan.style.color = '';
                receivedSpan.style.fontWeight = '';
              }, 500);
            }
          }

          if (bar) {
            const itemPct = item.dispatched > 0 ? (item.received / item.dispatched) * 100 : 0;
            bar.style.width = itemPct + '%';
          }

          if (item.complete) {
            completedCount++;
            if (statusIcon && !statusIcon.textContent.includes('✓')) {
              statusIcon.innerHTML = '<span style="font-size: 18px; animation: bounce 0.5s;">✓</span>';
              statusIcon.style.color = '#22c55e';
            } else if (statusIcon) {
              statusIcon.textContent = '✓';
              statusIcon.style.color = '#22c55e';
            }
            if (card) card.classList.add('completed');
          } else {
            if (statusIcon) statusIcon.textContent = '⏳';
            if (card) card.classList.remove('completed');
          }

          if (scannerName && item.receiver_name) {
            scannerName.textContent = item.receiver_name;
          }
        });

        document.getElementById('items-complete-badge').textContent =
          `${completedCount} / ${ITEM_COUNT} items complete`;
      }

      // Update recent activity
      if (data.recent_receives && data.recent_receives.length > 0) {
        const feedHtml = data.recent_receives.map(scan => `
          <div class="dm-activity-item">
            <div class="dm-activity-time">${scan.received_at}</div>
            <div class="dm-activity-desc">
              <span class="dm-activity-user">${scan.receiver_name || 'Unknown'}</span> received
              <span class="dm-activity-item-name">${scan.unique_code}</span>
              (${scan.item_name}) → <strong>${scan.destination}</strong>
            </div>
          </div>
        `).join('');
        document.getElementById('activity-feed').innerHTML = feedHtml;

        document.getElementById('undo-btn').disabled = false;
      }

      checkReceiveReady();
      failedAttempts = 0; // Reset on success
    })
    .catch(err => {
      failedAttempts++;
      console.error('Poll error (' + failedAttempts + '/' + MAX_FAILED_ATTEMPTS + '):', err.message);

      if (failedAttempts >= MAX_FAILED_ATTEMPTS) {
        clearInterval(pollInterval);
        alert('⚠️ Connection Lost - Please Refresh');
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
console.log('🚀 Starting real-time receive polling every 300ms...');
pollInterval = setInterval(pollProgress, 300);
pollProgress();

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
  if (pollInterval) {
    clearInterval(pollInterval);
  }
});

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

  checkReceiveReady();
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

  checkReceiveReady();
}

// ─── CHECK IF RECEIVE READY ─────────────────────────────────────
function checkReceiveReady() {
  const borrowedReady = BORROWED_COUNT === 0 || borrowedChecked.size === BORROWED_COUNT;
  const operationalReady = OPERATIONAL_COUNT === 0 || operationalChecked.size === OPERATIONAL_COUNT;

  const progressText = document.getElementById('progress-text').textContent;
  const match = progressText.match(/(\d+)\s*\/\s*(\d+)/);
  const receivedCount = match ? parseInt(match[1]) : 0;
  const totalCount = match ? parseInt(match[2]) : TOTAL_REQUIRED;
  const allReceived = receivedCount >= totalCount;

  const confirmBtn = document.getElementById('confirm-btn');
  if (allReceived && borrowedReady && operationalReady) {
    confirmBtn.disabled = false;
    confirmBtn.classList.add('enabled');
    confirmBtn.style.background = '#CC0000';
    confirmBtn.style.cursor = 'pointer';
  } else {
    confirmBtn.disabled = true;
    confirmBtn.classList.remove('enabled');
    confirmBtn.style.background = '#d0d0d0';
    confirmBtn.style.cursor = 'not-allowed';
  }
}

// ─── UNDO LAST RECEIVE (placeholder) ─────────────────────────────
function undoLastReceive() {
  alert('Undo feature coming soon!');
}

// ─── CONFIRM RECEIVE ────────────────────────────────────────────
function confirmReceive() {
  showConfirmModal({
    title: 'Confirm Complete Receive?',
    message: 'All received items will be processed and inventory updated.\nEvent status will be set to Completed.\n\nThis action cannot be undone.',
    confirmText: 'Confirm & Receive',
    type: 'success',
    onConfirm: function() {
      const btn = document.getElementById('confirm-btn');
      btn.disabled = true;
      btn.textContent = 'Processing...';

      fetch("{{ route('events.receive.confirm', [$event, $receiveSession]) }}", {
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

          btn.textContent = '✓ Received!';
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
          alert(data.message || 'Failed to complete receive');
          btn.textContent = '✓ Confirm Complete Receive';
          btn.disabled = false;
          btn.style.background = '#CC0000';
        }
      })
      .catch(err => {
        console.error('Receive error:', err);
        alert('Network error. Please check your connection and try again.');
        btn.textContent = '✓ Confirm Complete Receive';
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

// ─── DOWNLOAD RECEIVE NOTE ───────────────────────────────────────
async function downloadReceiveNote() {
  const btn = document.getElementById('download-note-btn');
  const btnText = document.getElementById('download-text');
  const originalText = btnText.textContent;

  btn.disabled = true;
  btnText.textContent = '⏳ Generating...';

  try {
    const response = await fetch("{{ route('events.receipt-note', [$event, $receiveSession]) }}", {
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
    a.download = 'RECEIVE-NOTE-{{ strtoupper(str_replace(" ", "-", $event->name)) }}.pdf';
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
    <button onclick="closeQRModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; color:#5c5550; cursor:pointer; padding:6px; border-radius:6px;">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
    <h3 style="font-size:16px; font-weight:700; color:#0f0f0f; margin:0 0 8px 0;">Receive Session QR Code</h3>
    <p style="font-size:12px; color:#5c5550; margin:0 0 20px 0;">{{ $event->name }}</p>
    <div style="background:#fff; border:2px solid #ece8e3; border-radius:12px; padding:20px; display:inline-block; margin-bottom:16px;">
      {!! $qrCodeSvg !!}
    </div>
    <p style="font-size:11px; color:#a09890; margin:0; font-family:'Courier New',monospace;">
      Token: {{ $receiveSession->session_token }}
    </p>
    <p style="font-size:11px; color:#a09890; margin:8px 0 0 0;">
      Expires: {{ $receiveSession->expires_at->format('d M Y, H:i') }} EAT
    </p>
  </div>
</div>

@endsection
