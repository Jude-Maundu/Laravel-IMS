@extends('layouts.app')
@section('title', 'Receive Session — ' . $event->name)
@section('page-title', 'Events')

@section('content')

<x-confirm-modal />

<style>
/* Receive Monitor Styles - Blue themed */
.rcv-monitor-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.rcv-monitor-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f0f0f;
    margin-bottom: 6px;
}
.rcv-monitor-sub {
    font-size: 13px;
    color: #5c5550;
}
.rcv-ref-badge {
    background: #185FA5;
    color: #ffffff;
    padding: 3px 10px;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    font-weight: 700;
}
.rcv-monitor-timer {
    text-align: right;
}
.rcv-countdown {
    font-size: 32px;
    font-weight: 700;
    color: #185FA5;
    font-family: 'Courier New', monospace;
    line-height: 1;
    margin-bottom: 6px;
}
.rcv-countdown-label {
    font-size: 11px;
    color: #a09890;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 10px;
}
.rcv-extend-btn {
    background: #185FA5;
    color: #ffffff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.rcv-extend-btn:hover {
    background: #14508a;
}

.scan-step-card {
    background: #ffffff;
    border: 1px solid #ece8e3;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    display: flex;
    gap: 20px;
}
.scan-step-num {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #185FA5;
    color: #ffffff;
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.scan-step-content {
    flex: 1;
}
.scan-step-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f0f0f;
    margin-bottom: 12px;
}
.scan-step-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.rcv-print-btn, .rcv-whatsapp-btn {
    background: #185FA5;
    color: #ffffff;
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s;
}
.rcv-print-btn:hover, .rcv-whatsapp-btn:hover {
    background: #14508a;
    color: #ffffff;
}
.rcv-whatsapp-btn {
    background: #25D366;
}
.rcv-whatsapp-btn:hover {
    background: #20BA5A;
}

.scan-qr-container {
    text-align: center;
    padding: 24px;
    background: #faf8f6;
    border-radius: 10px;
}
.scan-qr-container svg {
    max-width: 220px;
    height: auto;
    margin: 0 auto;
}
.scan-session-ref {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    font-weight: 700;
    color: #185FA5;
    margin-top: 12px;
}
.scan-session-status {
    font-size: 12px;
    color: #5c5550;
    margin-top: 6px;
}

.scan-progress-numbers {
    font-size: 16px;
    font-weight: 600;
    color: #0f0f0f;
    margin-bottom: 12px;
}
.scan-progress-sep {
    color: #a09890;
    margin: 0 6px;
}
.scan-progress-label {
    color: #5c5550;
    font-weight: 500;
}
.scan-progress-bar-wrap {
    height: 12px;
    background: #f5f1ed;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 16px;
}
.scan-progress-bar {
    height: 100%;
    background: #185FA5;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
.rcv-missing-count {
    font-size: 13px;
    color: #854F0B;
    background: #faeeda;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 16px;
    text-align: center;
}

.scan-item-breakdown {
    background: #faf8f6;
    border-radius: 10px;
    padding: 16px;
    margin-top: 20px;
}
.scan-breakdown-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f0f0f;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
}
.scan-breakdown-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}
.scan-breakdown-name {
    flex: 0 0 180px;
    font-size: 13px;
    color: #3a3530;
    font-weight: 500;
}
.scan-breakdown-bar-wrap {
    flex: 1;
    height: 8px;
    background: #e5e0db;
    border-radius: 4px;
    overflow: hidden;
}
.scan-breakdown-bar {
    height: 100%;
    background: #185FA5;
    transition: width 0.6s;
}
.scan-breakdown-count {
    flex: 0 0 70px;
    text-align: right;
    font-size: 12px;
    color: #5c5550;
    font-family: 'Courier New', monospace;
}
.scan-breakdown-status {
    flex: 0 0 20px;
    text-align: center;
    font-size: 16px;
    color: #185FA5;
}

.scan-recent-list {
    background: #faf8f6;
    border-radius: 10px;
    padding: 16px;
    margin-top: 20px;
}
.scan-recent-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f0f0f;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
}
.scan-recent-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    background: #ffffff;
    border-radius: 6px;
    margin-bottom: 6px;
    font-size: 12px;
}
.scan-recent-code {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: #0f0f0f;
}
.scan-recent-name {
    flex: 1;
    color: #5c5550;
}
.scan-recent-dest {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}
.rcv-dest-warehouse {
    background: #eaf3de;
    color: #3B6D11;
}
.rcv-dest-cleaning {
    background: #E1F5EE;
    color: #0F6E56;
}
.rcv-dest-repair {
    background: #fcebeb;
    color: #A32D2D;
}
.scan-recent-time {
    font-size: 11px;
    color: #a09890;
    font-family: 'Courier New', monospace;
}

.scan-switch-manual {
    display: inline-block;
    color: #185FA5;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 20px;
    border: 1.5px solid #185FA5;
    border-radius: 8px;
    transition: all 0.2s;
}
.scan-switch-manual:hover {
    background: #185FA5;
    color: #ffffff;
}
</style>

<div class="rcv-monitor-header">
    <div>
        <h1 class="rcv-monitor-title">Receive Session</h1>
        <div class="rcv-monitor-sub">
            {{ $event->name }} ·
            <span class="rcv-ref-badge">{{ $receiveSession->receive_ref }}</span>
        </div>
    </div>
    <div class="rcv-monitor-timer">
        <div class="rcv-countdown" id="countdown">--:--:--</div>
        <div class="rcv-countdown-label">session remaining</div>
        <button onclick="extendSession()" class="rcv-extend-btn">Extend</button>
    </div>
</div>

{{-- Step 1: Print or Share --}}
<div class="scan-step-card">
    <div class="scan-step-num">1</div>
    <div class="scan-step-content">
        <div class="scan-step-title">Print or share the receipt note</div>
        <div class="scan-step-actions">
            <a href="{{ route('events.receipt-note', [$event, $receiveSession]) }}"
               target="_blank" class="rcv-print-btn">
                🖨 Print Receipt Note
            </a>
            <a href="https://wa.me/?text={{ urlencode('Grey Apple IMS — Receive session for ' . $event->name . ' is ready. Open on your phone: ' . route('receive.show', $receiveSession->session_token)) }}"
               target="_blank" class="rcv-whatsapp-btn">
                💬 Share via WhatsApp
            </a>
        </div>
    </div>
</div>

{{-- Step 2: Session QR --}}
<div class="scan-step-card">
    <div class="scan-step-num">2</div>
    <div class="scan-step-content">
        <div class="scan-step-title">Warehouse team scans this QR to begin receiving</div>
        <div class="scan-qr-container">
            {!! $qrCodeSvg !!}
            <div class="scan-session-ref">{{ $receiveSession->receive_ref }}</div>
            <div class="scan-session-status" id="session-status">
                Waiting for first scan...
            </div>
        </div>
    </div>
</div>

{{-- Step 3: Progress --}}
<div class="scan-step-card">
    <div class="scan-step-num">3</div>
    <div class="scan-step-content">
        <div class="scan-step-title">Monitor receiving progress</div>
        <div class="scan-progress-numbers">
            <span id="received-count" style="color:#185FA5">
                {{ $receiveSession->received_count }}
            </span>
            <span class="scan-progress-sep"> of </span>
            <span id="total-count">{{ $totalDispatched }}</span>
            <span class="scan-progress-label"> items received</span>
        </div>
        <div class="scan-progress-bar-wrap">
            <div class="scan-progress-bar" id="main-progress"
                 style="width:{{ $totalDispatched > 0 ? round(($receiveSession->received_count / $totalDispatched) * 100) : 0 }}%">
            </div>
        </div>
        <div class="rcv-missing-count" id="missing-count-display">
            @php $missing = $event->missingItems()->where('status','missing')->count() @endphp
            @if($missing > 0)
                ⚠ {{ $missing }} item(s) marked missing
            @endif
        </div>

        {{-- Per-item breakdown --}}
        <div class="scan-item-breakdown" id="item-breakdown">
            <div class="scan-breakdown-title">Item Progress</div>
            @foreach($itemProgress as $item)
            <div class="scan-breakdown-row" id="item-row-{{ $item['item_id'] }}">
                <div class="scan-breakdown-name">{{ $item['item_name'] }}</div>
                <div class="scan-breakdown-bar-wrap">
                    <div class="scan-breakdown-bar"
                         style="width:{{ $item['dispatched'] > 0 ? round(($item['received'] / $item['dispatched']) * 100) : 0 }}%"
                         id="item-bar-{{ $item['item_id'] }}">
                    </div>
                </div>
                <div class="scan-breakdown-count" id="item-count-{{ $item['item_id'] }}">
                    {{ $item['received'] }} / {{ $item['dispatched'] }}
                </div>
                <div class="scan-breakdown-status" id="item-status-{{ $item['item_id'] }}">
                    {{ $item['complete'] ? '✓' : '' }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Recent receives --}}
        <div class="scan-recent-list" id="recent-list">
            <div class="scan-recent-title">Recently Received</div>
            <div id="recent-items"></div>
        </div>
    </div>
</div>

{{-- Step 4: Borrowed Items (if any) --}}
@if($event->borrowedItems->count() > 0)
<div class="scan-step-card">
    <div class="scan-step-num">4</div>
    <div class="scan-step-content">
        <div class="scan-step-title">Confirm borrowed items received ({{ $event->borrowedItems->count() }})</div>
        <div class="scan-item-breakdown">
            <div class="scan-breakdown-title">Borrowed Items Checklist</div>
            @foreach($event->borrowedItems as $borrowed)
            <div class="scan-breakdown-row" id="borrowed-row-{{ $borrowed->id }}">
                <input type="checkbox"
                       id="borrowed-check-{{ $borrowed->id }}"
                       class="rcv-checkbox borrowed-checkbox"
                       data-id="{{ $borrowed->id }}"
                       onchange="toggleBorrowedCheck({{ $borrowed->id }})"
                       style="width:18px;height:18px;cursor:pointer;margin-right:8px">
                <div class="scan-breakdown-name" style="flex:1">
                    {{ $borrowed->item_name }}
                    <span style="color:#7c7470;font-size:11px;font-weight:400;margin-left:6px">
                        — {{ $borrowed->source_company }}
                    </span>
                </div>
                <div class="scan-breakdown-count">
                    {{ $borrowed->quantity_dispatched }} pcs
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:12px;font-size:11px;color:#5c5550">
            ✓ <span id="borrowed-checked-count">0</span> of {{ $event->borrowedItems->count() }} confirmed
        </div>
    </div>
</div>
@endif

{{-- Step 5: Operational Items (if any) --}}
@if($event->operationalItems->count() > 0)
<div class="scan-step-card">
    <div class="scan-step-num">{{ $event->borrowedItems->count() > 0 ? '5' : '4' }}</div>
    <div class="scan-step-content">
        <div class="scan-step-title">Confirm operational items received ({{ $event->operationalItems->count() }})</div>
        <div class="scan-item-breakdown">
            <div class="scan-breakdown-title">Operational Items Checklist</div>
            @foreach($event->operationalItems as $opItem)
            <div class="scan-breakdown-row" id="operational-row-{{ $opItem->id }}">
                <input type="checkbox"
                       id="operational-check-{{ $opItem->id }}"
                       class="rcv-checkbox operational-checkbox"
                       data-id="{{ $opItem->id }}"
                       onchange="toggleOperationalCheck({{ $opItem->id }})"
                       style="width:18px;height:18px;cursor:pointer;margin-right:8px">
                <div class="scan-breakdown-name" style="flex:1">
                    {{ $opItem->operationalItem->name ?? $opItem->custom_name }}
                </div>
                <div class="scan-breakdown-count">
                    {{ $opItem->quantity_dispatched }} pcs
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:12px;font-size:11px;color:#5c5550">
            ✓ <span id="operational-checked-count">0</span> of {{ $event->operationalItems->count() }} confirmed
        </div>
    </div>
</div>
@endif

{{-- Final Step: Confirm & Complete --}}
<div class="scan-step-card" style="border:2px solid #185FA5">
    <div class="scan-step-num" style="background:#3B6D11">✓</div>
    <div class="scan-step-content">
        <div class="scan-step-title">Confirm & Complete Receive</div>
        <p style="font-size:13px;color:#5c5550;margin-bottom:16px">
            Once all items are received and verified, complete the receive session to update inventory.
        </p>
        <form method="POST" action="{{ route('events.receive.confirm', [$event, $receiveSession]) }}" id="complete-form">
            @csrf
            <input type="hidden" name="borrowed_checked" id="borrowed-checked-input" value="">
            <input type="hidden" name="operational_checked" id="operational-checked-input" value="">
            <button type="button"
                    onclick="confirmComplete()"
                    id="complete-btn"
                    disabled
                    style="background:#ccc;color:#fff;border:none;padding:14px 28px;border-radius:8px;font-size:14px;font-weight:700;cursor:not-allowed;transition:all 0.2s">
                Confirm & Complete Receive
            </button>
        </form>
        <div style="margin-top:12px;font-size:11px;color:#854F0B" id="complete-status">
            ⏳ Waiting for all items to be received...
        </div>
    </div>
</div>

{{-- Switch to Manual --}}
<div style="text-align:center;padding:24px">
    <a href="{{ route('events.receive.manual', $event) }}"
       class="scan-switch-manual"
       onclick="return confirm('Switch to manual receive? The scan session will remain active.')">
        Switch to Manual Receive
    </a>
</div>

@endsection

@section('scripts')
<script>
const PROGRESS_URL   = "{{ route('events.receive.progress', [$event, $receiveSession]) }}";
const EXTEND_URL     = "{{ route('events.receive.extend', [$event, $receiveSession]) }}";
const CSRF           = "{{ csrf_token() }}";
const EXPIRES_AT     = new Date("{{ $receiveSession->expires_at->toISOString() }}");

// Countdown timer
function updateCountdown() {
    const now  = new Date();
    const diff = EXPIRES_AT - now;
    if (diff <= 0) {
        document.getElementById('countdown').textContent = 'EXPIRED';
        return;
    }
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    document.getElementById('countdown').textContent =
        String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
}
setInterval(updateCountdown, 1000);
updateCountdown();

// Extend session
async function extendSession() {
    const res  = await fetch(EXTEND_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.success) {
        EXPIRES_AT.setTime(new Date(data.expires_at).getTime());
        alert('Session extended by 4 hours.');
    }
}

// Poll progress every 5 seconds
async function pollProgress() {
    try {
        const res  = await fetch(PROGRESS_URL, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        document.getElementById('received-count').textContent = data.received_count;

        const pct = data.total_dispatched > 0
            ? (data.received_count / data.total_dispatched) * 100 : 0;
        document.getElementById('main-progress').style.width = pct + '%';

        if (data.missing_count > 0) {
            document.getElementById('missing-count-display').innerHTML =
                '⚠ ' + data.missing_count + ' item(s) marked missing';
            document.getElementById('missing-count-display').style.display = 'block';
        }

        // Update session status
        if (data.received_count > 0) {
            document.getElementById('session-status').textContent = 'Session Active';
            document.getElementById('session-status').style.color = '#185FA5';
        }

        // Update item breakdown
        if (data.item_progress) {
            data.item_progress.forEach(function(item) {
                const countEl  = document.getElementById('item-count-' + item.item_id);
                const barEl    = document.getElementById('item-bar-'   + item.item_id);
                const statusEl = document.getElementById('item-status-'+ item.item_id);
                const rowEl    = document.getElementById('item-row-'   + item.item_id);

                if (countEl) countEl.textContent = item.received + ' / ' + item.dispatched;
                if (barEl) {
                    const p = item.dispatched > 0 ? (item.received / item.dispatched) * 100 : 0;
                    barEl.style.width = p + '%';
                }
                if (statusEl) {
                    statusEl.textContent = item.complete ? '✓' : '';
                    statusEl.style.color = item.complete ? '#185FA5' : '';
                }
                if (rowEl && item.complete) rowEl.style.opacity = '0.6';
            });
        }

        // Update recent receives list
        if (data.recent_receives && data.recent_receives.length > 0) {
            const html = data.recent_receives.map(r =>
                '<div class="scan-recent-item">' +
                '<span class="scan-recent-code">' + r.unique_code + '</span>' +
                '<span class="scan-recent-name">' + r.item_name + '</span>' +
                '<span class="scan-recent-dest rcv-dest-' + r.destination + '">' + r.destination + '</span>' +
                '<span class="scan-recent-time">' + r.received_at + '</span>' +
                '</div>'
            ).join('');
            document.getElementById('recent-items').innerHTML = html;
        }

    } catch(e) {}
}

setInterval(pollProgress, 5000);
pollProgress();

// Borrowed and operational items tracking
const borrowedChecked = new Set();
const operationalChecked = new Set();
const borrowedCount = {{ $event->borrowedItems->count() }};
const operationalCount = {{ $event->operationalItems->count() }};
const TOTAL_REQUIRED = {{ $totalDispatched }};

function toggleBorrowedCheck(id) {
    const checkbox = document.getElementById('borrowed-check-' + id);
    if (checkbox.checked) {
        borrowedChecked.add(id);
    } else {
        borrowedChecked.delete(id);
    }
    updateBorrowedCount();
    checkCompleteReady();
}

function toggleOperationalCheck(id) {
    const checkbox = document.getElementById('operational-check-' + id);
    if (checkbox.checked) {
        operationalChecked.add(id);
    } else {
        operationalChecked.delete(id);
    }
    updateOperationalCount();
    checkCompleteReady();
}

function updateBorrowedCount() {
    const countEl = document.getElementById('borrowed-checked-count');
    if (countEl) countEl.textContent = borrowedChecked.size;
}

function updateOperationalCount() {
    const countEl = document.getElementById('operational-checked-count');
    if (countEl) countEl.textContent = operationalChecked.size;
}

function checkCompleteReady() {
    const completeBtn = document.getElementById('complete-btn');
    const statusEl = document.getElementById('complete-status');

    // Get current received count from the display
    const receivedCount = parseInt(document.getElementById('received-count').textContent);

    const allReceived = receivedCount >= TOTAL_REQUIRED;
    const borrowedReady = borrowedCount === 0 || borrowedChecked.size === borrowedCount;
    const operationalReady = operationalCount === 0 || operationalChecked.size === operationalCount;

    if (allReceived && borrowedReady && operationalReady) {
        completeBtn.disabled = false;
        completeBtn.style.background = '#3B6D11';
        completeBtn.style.cursor = 'pointer';
        statusEl.innerHTML = '✅ All items received — ready to complete!';
        statusEl.style.color = '#3B6D11';
    } else {
        completeBtn.disabled = true;
        completeBtn.style.background = '#ccc';
        completeBtn.style.cursor = 'not-allowed';

        let msg = '⏳ ';
        if (!allReceived) msg += 'Receive all pieces. ';
        if (!borrowedReady) msg += 'Confirm all borrowed items. ';
        if (!operationalReady) msg += 'Confirm all operational items.';
        statusEl.innerHTML = msg;
        statusEl.style.color = '#854F0B';
    }
}

function confirmComplete() {
    const receivedCount = parseInt(document.getElementById('received-count').textContent);

    showConfirmModal({
        title: 'Complete Receive Session',
        message: `Confirm receiving complete?\n\n${receivedCount} pieces received\n${borrowedChecked.size} borrowed items\n${operationalChecked.size} operational items\n\nThis will update all item statuses and locations.`,
        confirmText: 'Complete Receive',
        type: 'success',
        onConfirm: function() {
            // Update hidden inputs
            document.getElementById('borrowed-checked-input').value = JSON.stringify([...borrowedChecked]);
            document.getElementById('operational-checked-input').value = JSON.stringify([...operationalChecked]);
            // Submit form
            document.getElementById('complete-form').submit();
        }
    });
}

// Check on page load and after each poll
const originalPollProgress = pollProgress;
pollProgress = async function() {
    await originalPollProgress();
    checkCompleteReady();
};
checkCompleteReady();
</script>
@endsection
