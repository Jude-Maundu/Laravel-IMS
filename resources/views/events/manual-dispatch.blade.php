@extends('layouts.app')
@section('title', 'Manual Dispatch — ' . $event->name)
@section('page-title', 'Events')

@section('content')

{{-- BREADCRUMB --}}
<div class="wiz-breadcrumb">
  <a href="{{ route('events.show', $event) }}" class="wiz-back-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    {{ $event->name }}
  </a>
  <span class="wiz-bc-sep">/</span>
  <span class="wiz-bc-current">Manual Dispatch</span>
</div>

{{-- WARNING BANNER --}}
<div style="background: #faeeda; border: 1px solid #f3ddb7; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px; display: flex; align-items: start; gap: 10px;">
  <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round" style="flex-shrink: 0; margin-top: 2px;"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg>
  <div>
    <div style="font-size: 13px; font-weight: 600; color: #854F0B; margin-bottom: 4px;">Manual Dispatch</div>
    <div style="font-size: 11px; color: #a0710f; line-height: 1.5;">
      Enter piece codes from the QR stickers on each item. Use the last 3 digits of the unique code (e.g., for GA-FOG-007, enter <strong>007</strong>).
    </div>
  </div>
</div>

<form method="POST" action="{{ route('events.dispatch.manual.store', $event) }}" id="dispatch-form">
  @csrf

  {{-- SECTION A: OWN INVENTORY --}}
  @if($event->eventItems->count() > 0)
  <div class="disp-section">
    <h2 class="disp-section-title">Grey Apple Inventory</h2>

    @foreach($event->eventItems as $eventItem)
    @php
      $item = $eventItem->item;
      if (!$item) continue;
      $shortcode = substr($item->category, 0, 3);
      $prefix = 'GA-' . strtoupper($shortcode) . '-';
      $qtyNeeded = $eventItem->quantity_requested;
    @endphp
    <div class="disp-item-card">
      <div class="disp-item-header">
        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
          @if($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="disp-item-thumb">
          @else
            <div class="disp-item-thumb" style="background: #f0ece8; display: flex; align-items: center; justify-content: center;">
              <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#a09890" stroke-width="1.5"><rect x="2" y="3" width="12" height="10" rx="1"/><path d="M2 10l3-3 3 3 5-5"/></svg>
            </div>
          @endif
          <div>
            <div style="font-size: 13px; font-weight: 700; color: #0f0f0f;">{{ $item->name }}</div>
            <div style="font-size: 11px; color: #a09890;">{{ $item->category }} · {{ $prefix }}___</div>
          </div>
        </div>
        <div style="font-size: 12px; font-weight: 600; color: #5c5550;">{{ $qtyNeeded }} piece{{ $qtyNeeded > 1 ? 's' : '' }} needed</div>
      </div>

      <div style="border-top: 1px solid #f5f1ed; padding-top: 12px; margin-top: 12px;">
        @for($i = 0; $i < $qtyNeeded; $i++)
        <div class="disp-piece-row">
          <span class="disp-piece-prefix">{{ $prefix }}</span>
          <input type="text"
                 name="dispatched_pieces[{{ $item->id }}][]"
                 class="disp-piece-input"
                 maxlength="3"
                 pattern="[0-9]{3}"
                 placeholder="___"
                 data-item-id="{{ $item->id }}"
                 data-prefix="{{ $prefix }}"
                 onblur="validatePieceCode(this)">
          <div class="disp-piece-status"></div>
        </div>
        @endfor

        <button type="button" class="disp-auto-assign" onclick="autoAssignPieces({{ $item->id }}, {{ $qtyNeeded }})">
          Auto-assign available pieces
        </button>

        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #f8f6f3;">
          <div style="font-size: 11px; font-weight: 600; color: #5c5550; margin-bottom: 6px;">Condition:</div>
          <div class="disp-condition-btns">
            @for($c = 1; $c <= 5; $c++)
            <button type="button"
                    class="disp-condition-btn {{ $c === 5 ? 'disp-condition-btn-active' : '' }}"
                    data-item-id="{{ $item->id }}"
                    onclick="selectCondition({{ $item->id }}, {{ $c }})">
              {{ $c }}
            </button>
            @endfor
          </div>
          <input type="hidden" name="conditions[{{ $item->id }}]" value="5" id="condition-{{ $item->id }}">
        </div>

        <div style="margin-top: 12px;">
          <label style="font-size: 11px; font-weight: 600; color: #5c5550; margin-bottom: 4px; display: block;">Notes (optional):</label>
          <textarea name="dispatch_notes[{{ $item->id }}]" class="disp-notes-input" rows="2" placeholder="Any notes about this dispatch..."></textarea>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- SECTION B: BORROWED ITEMS --}}
  @if($event->borrowedItems->count() > 0)
  <div class="disp-section">
    <h2 class="disp-section-title">Borrowed Items</h2>
    @foreach($event->borrowedItems as $borrowed)
    <div class="disp-item-card" style="padding: 12px 16px;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <div style="font-size: 13px; font-weight: 700; color: #0f0f0f;">{{ $borrowed->item_name }}</div>
          <div style="font-size: 11px; color: #a09890;">{{ $borrowed->source_company }}</div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <label style="font-size: 11px; color: #5c5550;">Qty:</label>
          <input type="number"
                 name="borrowed_confirmed[{{ $borrowed->id }}]"
                 class="disp-qty-input"
                 value="{{ $borrowed->quantity_dispatched }}"
                 min="0"
                 style="width: 60px;">
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- SECTION C: OPERATIONAL ITEMS --}}
  @if($event->operationalItems->count() > 0)
  <div class="disp-section">
    <h2 class="disp-section-title">Operational Items</h2>
    @foreach($event->operationalItems as $opItem)
    <div class="disp-item-card" style="padding: 12px 16px;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <div style="font-size: 13px; font-weight: 700; color: #0f0f0f;">
            {{ $opItem->operationalItem->name ?? $opItem->custom_name }}
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <label style="font-size: 11px; color: #5c5550;">Qty:</label>
          <input type="number"
                 name="operational_confirmed[{{ $opItem->id }}]"
                 class="disp-qty-input"
                 value="{{ $opItem->quantity_dispatched }}"
                 min="0"
                 style="width: 60px;">
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- FOOTER --}}
  <div class="wiz-footer">
    <div class="wiz-footer-hint">Review all piece codes before submitting</div>
    <div class="wiz-footer-actions">
      <a href="{{ route('events.show', $event) }}" class="wiz-btn-secondary">Back to Event</a>
      <button type="submit" class="wiz-btn-primary">Submit Dispatch</button>
    </div>
  </div>
</form>

<script>
// Validate piece code on blur
function validatePieceCode(input) {
  const code = input.value.trim();
  const itemId = input.dataset.itemId;
  const prefix = input.dataset.prefix;
  const status = input.parentElement.querySelector('.disp-piece-status');

  if (!code) {
    status.innerHTML = '';
    input.classList.remove('disp-piece-valid', 'disp-piece-invalid');
    return;
  }

  status.innerHTML = '<span style="font-size:10px;color:#a09890;">Checking...</span>';

  const fullCode = prefix + code;

  fetch(`/api/pieces/validate?code=${encodeURIComponent(fullCode)}&item_id=${itemId}`)
    .then(res => res.json())
    .then(data => {
      if (data.valid) {
        input.classList.add('disp-piece-valid');
        input.classList.remove('disp-piece-invalid');
        status.innerHTML = '<span style="font-size:10px;color:#3B6D11;">✓ Valid</span>';
      } else {
        input.classList.add('disp-piece-invalid');
        input.classList.remove('disp-piece-valid');
        status.innerHTML = '<span style="font-size:10px;color:#CC0000;">✗ ' + data.message + '</span>';
      }
    })
    .catch(() => {
      status.innerHTML = '<span style="font-size:10px;color:#CC0000;">Error checking</span>';
    });
}

// Select condition
function selectCondition(itemId, condition) {
  const buttons = document.querySelectorAll(`[data-item-id="${itemId}"].disp-condition-btn`);
  buttons.forEach(btn => btn.classList.remove('disp-condition-btn-active'));
  event.target.classList.add('disp-condition-btn-active');
  document.getElementById(`condition-${itemId}`).value = condition;
}

// Auto-assign available pieces
function autoAssignPieces(itemId, qtyNeeded) {
  const btn = event.target;
  btn.textContent = 'Loading...';
  btn.disabled = true;

  fetch(`/api/items/${itemId}/availability`)
    .then(res => res.json())
    .then(data => {
      if (data.available_pieces && data.available_pieces.length > 0) {
        const inputs = document.querySelectorAll(`input[name="dispatched_pieces[${itemId}][]"]`);
        const pieces = data.available_pieces.slice(0, qtyNeeded);

        pieces.forEach((piece, index) => {
          if (inputs[index]) {
            // Extract last 3 digits from unique_code
            const lastThree = piece.unique_code.slice(-3);
            inputs[index].value = lastThree;
            validatePieceCode(inputs[index]);
          }
        });

        btn.textContent = 'Pieces assigned';
        setTimeout(() => {
          btn.textContent = 'Auto-assign available pieces';
          btn.disabled = false;
        }, 2000);
      } else {
        btn.textContent = 'No pieces available';
        setTimeout(() => {
          btn.textContent = 'Auto-assign available pieces';
          btn.disabled = false;
        }, 2000);
      }
    })
    .catch(() => {
      btn.textContent = 'Error';
      btn.disabled = false;
    });
}

// Form submission validation
document.getElementById('dispatch-form').addEventListener('submit', function(e) {
  const invalidInputs = document.querySelectorAll('.disp-piece-invalid');
  if (invalidInputs.length > 0) {
    e.preventDefault();
    alert('Some piece codes are invalid. Please fix them before submitting.');
    return false;
  }

  const allInputs = document.querySelectorAll('.disp-piece-input');
  let emptyCount = 0;
  allInputs.forEach(input => {
    if (!input.value.trim()) emptyCount++;
  });

  if (emptyCount > 0) {
    if (!confirm(`${emptyCount} piece code(s) are empty. Continue anyway?`)) {
      e.preventDefault();
      return false;
    }
  }
});
</script>

@endsection
