@extends('layouts.app')
@section('title', 'Site-to-Site Link — ' . $event->name)
@section('page-title', 'Events')

@section('content')

{{-- PAGE HEADER --}}
<div class="wiz-page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <a href="{{ route('events.show', $event) }}" class="wiz-back-link">{{ $event->name }}</a>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550;font-weight:500">Site-to-Site Link</span>
    </div>
    <h1 class="wiz-page-title">Create Site-to-Site Linked Event</h1>
    <p style="font-size:13px;color:#7c7470;margin-top:8px;max-width:680px">Transfer items from <strong>{{ $event->name }}</strong> directly to a new event site without returning to warehouse. Items not needed can be returned to warehouse.</p>
  </div>
</div>

{{-- CUSTOM STEP INDICATOR FOR SITE-TO-SITE --}}
<div class="wiz-stepper">
  <div class="wiz-step wiz-step-active">
    <div class="wiz-step-num">1</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Event Details & Items</span>
      <span class="wiz-step-sub">Configure new event</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">2</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Team</span>
      <span class="wiz-step-sub">Assign crew</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">3</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Dispatch</span>
      <span class="wiz-step-sub">Photos & condition</span>
    </div>
  </div>
</div>

<div class="wiz-layout">

  {{-- SIDEBAR --}}
  <div class="wiz-sidebar">
    <div class="wiz-sidebar-card" style="background:#fff8f8;border-color:#fde8e8">
      <div class="wiz-sidebar-title" style="color:#CC0000">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="display:inline-block;vertical-align:middle;margin-right:6px">
          <path d="M2 8h5M9 8h5M7 3l-5 5 5 5M9 3l5 5-5 5"/>
        </svg>
        Source Event
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Name</span>
        <span class="wiz-sum-val">{{ $event->name }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Client</span>
        <span class="wiz-sum-val">{{ $event->client_name }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Venue</span>
        <span class="wiz-sum-val">{{ $event->venue }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Pending Items</span>
        <span class="wiz-sum-val" style="color:#CC0000;font-weight:700">{{ count($preSelectedIds) }}</span>
      </div>
    </div>
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Selection</div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Items to Transfer</span>
        <span class="wiz-sum-val" id="selected-count" style="color:#185FA5;font-weight:700">{{ count($preSelectedIds) }} items</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Return to Warehouse</span>
        <span class="wiz-sum-val" id="return-count" style="color:#3B6D11">0 items</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">From Warehouse</span>
        <span class="wiz-sum-val" style="color:#7c7470">
          {{ $categories->flatten()->where('status','Available')->count() }} available
        </span>
      </div>
    </div>
    <div class="wiz-sidebar-card" style="background:#f0f7ff;border-color:#dae8f7">
      <div style="font-size:10px;font-weight:700;color:#185FA5;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px">📋 How it works</div>
      <div style="font-size:11px;color:#5c5550;line-height:1.7;">
        <div style="background:#fff;padding:10px;border-radius:6px;margin-bottom:8px;border:1px solid #dae8f7">
          <div style="font-weight:700;color:#185FA5;margin-bottom:4px">✓ Ticked items:</div>
          <div style="font-size:10px">Transfer to new site → Receive at new event</div>
        </div>
        <div style="background:#fff;padding:10px;border-radius:6px;border:1px solid #dae8f7">
          <div style="font-weight:700;color:#CC0000;margin-bottom:4px">☐ Unticked items:</div>
          <div style="font-size:10px">Return to warehouse → Receive at current event</div>
        </div>
      </div>
    </div>
  </div>

  {{-- MAIN CONTENT --}}
  <div class="wiz-main">
    <form method="POST" action="{{ route('events.site-to-site.create', $event) }}" id="s2s-form">
      @csrf

      {{-- EVENT DETAILS CARD --}}
      <div class="wiz-card" style="margin-bottom:16px">
        <div class="wiz-card-head">
          <div class="wiz-card-title">New Event Details</div>
          <div class="wiz-card-sub">Provide details for the linked event</div>
        </div>
        <div class="wiz-card-body">
          <div class="wiz-form-row">
            <div class="wiz-form-group">
              <label class="wiz-label">Event Name <span style="color:#CC0000">*</span></label>
              <input type="text" name="name" class="wiz-input" required value="{{ old('name') }}" placeholder="e.g., {{ $event->client_name }} — Follow-up Event">
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Client Name <span style="color:#CC0000">*</span></label>
              <input type="text" name="client_name" class="wiz-input" required value="{{ old('client_name', $event->client_name) }}">
            </div>
          </div>

          <div class="wiz-form-row">
            <div class="wiz-form-group">
              <label class="wiz-label">Venue <span style="color:#CC0000">*</span></label>
              <input type="text" name="venue" class="wiz-input" required value="{{ old('venue') }}" placeholder="New venue location">
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Location Name</label>
              <input type="text" name="location_name" class="wiz-input" value="{{ old('location_name') }}" placeholder="e.g., Main Hall">
            </div>
          </div>

          <div class="wiz-form-row">
            <div class="wiz-form-group">
              <label class="wiz-label">Loading Date <span style="color:#CC0000">*</span></label>
              <input type="date" name="loading_date" class="wiz-input" required value="{{ old('loading_date', $event->setdown_date->addDay()->format('Y-m-d')) }}">
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Setup Date <span style="color:#CC0000">*</span></label>
              <input type="date" name="setup_date" class="wiz-input" required value="{{ old('setup_date', $event->setdown_date->addDays(2)->format('Y-m-d')) }}">
            </div>
          </div>

          <div class="wiz-form-row">
            <div class="wiz-form-group">
              <label class="wiz-label">Event Date <span style="color:#CC0000">*</span></label>
              <input type="date" name="event_date" class="wiz-input" required value="{{ old('event_date', $event->setdown_date->addDays(3)->format('Y-m-d')) }}">
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Set-down Date <span style="color:#CC0000">*</span></label>
              <input type="date" name="setdown_date" class="wiz-input" required value="{{ old('setdown_date', $event->setdown_date->addDays(4)->format('Y-m-d')) }}">
            </div>
          </div>

          <div class="wiz-form-row">
            <div class="wiz-form-group">
              <label class="wiz-label">Estimated Cost (KES)</label>
              <input type="number" name="cost" class="wiz-input" min="0" step="0.01" value="{{ old('cost') }}" placeholder="0.00">
            </div>
          </div>

          <div class="wiz-form-group">
            <label class="wiz-label">Notes</label>
            <textarea name="notes" class="wiz-input" rows="3" placeholder="Any additional notes...">{{ old('notes', 'Site-to-site transfer from ' . $event->name) }}</textarea>
          </div>
        </div>
      </div>

      {{-- ITEM SELECTION CARD --}}
      <div class="wiz-card">
        <div class="wiz-card-head" style="display:flex;align-items:center;justify-content:space-between">
          <div>
            <div class="wiz-card-title">Item Selection & Transfer</div>
            <div class="wiz-card-sub">Items from <strong>{{ $event->name }}</strong> are pre-selected. Uncheck to return to warehouse, or add items from inventory.</div>
          </div>
          <span class="wiz-selected-badge" id="selected-badge">{{ count($preSelectedIds) }} selected</span>
        </div>
        <div class="wiz-card-body">

          {{-- INSTRUCTIONS --}}
          <div style="background:#fff8f8;border:1px solid #fde8e8;border-radius:10px;padding:16px;margin-bottom:20px">
            <div style="display:flex;gap:12px;align-items:start">
              <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#CC0000" stroke-width="1.5" stroke-linecap="round" style="flex-shrink:0;margin-top:2px">
                <circle cx="8" cy="8" r="6.5"/><line x1="8" y1="7" x2="8" y2="11"/><circle cx="8" cy="5" r="0.5" fill="#CC0000"/>
              </svg>
              <div>
                <div style="font-size:12px;font-weight:700;color:#CC0000;margin-bottom:6px">Important: How to Select Items</div>
                <ul style="font-size:11px;color:#5c5550;line-height:1.7;margin:0;padding-left:18px">
                  <li><strong>Ticked (✓) items</strong> will be <span style="color:#185FA5;font-weight:600">transferred to the new site</span> and must be received there</li>
                  <li><strong>Unticked (☐) items</strong> will be <span style="color:#3B6D11;font-weight:600">returned to warehouse</span> and received at the current event</li>
                  <li>All items are ticked by default — simply <strong>untick</strong> any item you don't want to transfer</li>
                </ul>
              </div>
            </div>
          </div>

          {{-- ITEMS FROM CURRENT EVENT --}}
          <div style="background:#f0f7ff;border:1px solid #dae8f7;border-radius:10px;padding:18px;margin-bottom:20px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round">
                <rect x="2" y="2" width="12" height="12" rx="2"/>
                <line x1="5" y1="6" x2="11" y2="6"/>
                <line x1="5" y1="9" x2="9" y2="9"/>
              </svg>
              <span style="font-size:12px;font-weight:700;color:#185FA5;text-transform:uppercase;letter-spacing:0.05em">Items from {{ $event->name }}</span>
              <span style="font-size:11px;color:#a09890;margin-left:auto">{{ count($preSelectedIds) }} items (all ticked by default)</span>
            </div>

            @foreach($pendingItems as $eventItem)
            @php 
              $item = $eventItem->item;
              $availableCount = $item->pieces()->where('status', 'Available')->count() + $item->pieces()->where('status', 'Assigned')->where('current_event_id', $event->id)->count();
              $maxQuantity = min($eventItem->quantity_requested, $availableCount);
            @endphp
            <div class="wiz-item-row" data-name="{{ strtolower($item->name) }}" data-source="current-event">
              <div class="wiz-item-check wiz-check-on" onclick="toggleCurrentEventItem(this, {{ $eventItem->id }})">
                <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
                <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="wiz-hidden-check" checked>
                <input type="checkbox" name="items_to_return[]" value="{{ $eventItem->id }}" class="wiz-hidden-return-check" style="display:none">
              </div>
              <span class="wiz-item-name">{{ $item->name }}</span>
              <span class="wiz-item-id">#ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
              <div class="wiz-item-qty">
                <input type="number" name="quantities[{{ $item->id }}]" value="{{ $maxQuantity }}" min="0" max="{{ $maxQuantity }}" class="wiz-qty-input" onchange="updateCount()">
                <span class="wiz-qty-label">of {{ $availableCount }} available</span>
              </div>
              <span class="wiz-tag transfer-tag" style="background:#f0f7ff;color:#185FA5;border-color:#dae8f7">Will transfer</span>
            </div>
            @endforeach
          </div>

          {{-- SEARCH --}}
          <div style="display:flex;gap:8px;margin-bottom:14px;align-items:center">
            <div class="wiz-checklist-search" style="flex:1">
              <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="#b0a8a0" stroke-width="1.5" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><line x1="10.5" y1="10.5" x2="14" y2="14"/></svg>
              <input type="text" id="checklist-search" placeholder="Search available items from warehouse..." class="wiz-search-input">
            </div>
            <span style="font-size:11px;color:#a09890">Add items from warehouse if needed</span>
          </div>

          {{-- CATEGORIES FROM WAREHOUSE --}}
          @foreach($categories as $categoryName => $items)
          @php
            // Exclude items already in current event from the count
            $availableItems = $items->where('status','Available')->reject(function($item) use ($preSelectedIds) {
              return in_array($item->id, $preSelectedIds);
            });
            $availableInCat = $availableItems->count();
            $totalInCat = $items->count();
          @endphp
          @if($availableInCat > 0)
          <div class="wiz-cat-group" data-category="{{ strtolower($categoryName) }}">
            <div class="wiz-cat-head">
              <div class="wiz-cat-check {{ $availableInCat === 0 ? 'wiz-check-disabled' : '' }}"
                   id="cat-check-{{ $loop->index }}"
                   data-cat="{{ $loop->index }}"
                   onclick="{{ $availableInCat > 0 ? 'toggleCategory(this)' : '' }}">
                <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
              </div>
              <span class="wiz-cat-name">{{ $categoryName }}</span>
              <span class="wiz-cat-count">{{ $availableInCat }} additional available</span>
            </div>

            @foreach($items->where('status', 'Available') as $item)
            @php
              $isSelected = in_array($item->id, $preSelectedIds);
              // Skip items that are already in the current event section to avoid duplicates
              if (in_array($item->id, $preSelectedIds)) {
                continue;
              }
              $availableCount = $item->pieces()->where('status', 'Available')->count();
            @endphp
            <div class="wiz-item-row checklist-item" data-name="{{ strtolower($item->name) }}" data-source="warehouse">
              <div class="wiz-item-check {{ $isSelected ? 'wiz-check-on' : '' }}"
                   data-cat="{{ $loop->parent->index }}"
                   onclick="toggleWarehouseItem(this)">
                <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
                <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="wiz-hidden-check" {{ $isSelected ? 'checked' : '' }}>
              </div>
              <span class="wiz-item-name">{{ $item->name }}</span>
              <span class="wiz-item-id">#ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
              <div class="wiz-item-qty">
                <input type="number" name="quantities[{{ $item->id }}]" value="0" min="0" max="{{ $availableCount }}" class="wiz-qty-input" onchange="updateCount()">
                <span class="wiz-qty-label">of {{ $availableCount }} available</span>
              </div>
              <span class="wiz-tag wiz-tag-avail">Available</span>
            </div>
            @endforeach

          </div>
          @endif
          @endforeach

        </div>
        <div class="wiz-card-footer">
          <span class="wiz-footer-hint" id="footer-count">{{ count($preSelectedIds) }} items selected for transfer</span>
          <div class="wiz-footer-actions">
            <a href="{{ route('events.show', $event) }}" class="wiz-btn-cancel">← Cancel</a>
            <button type="submit" class="wiz-btn-next" id="s2s-submit">
              Create Linked Event & Continue
              <svg width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
            </button>
          </div>
        </div>
      </div>

    </form>
  </div>

</div>

<script>
(function() {
  var selectedCount = {{ count($preSelectedIds) }};
  var returnCount = 0;

  function parseQty(input) {
    if (!input) return 0;
    var value = parseInt(input.value, 10);
    return isNaN(value) || value < 0 ? 0 : value;
  }

  function updateCount() {
    var selectedCount = 0;
    var returnCount = 0;

    document.querySelectorAll('.wiz-item-row').forEach(function(row) {
      var checkbox = row.querySelector('.wiz-hidden-check');
      var qtyInput = row.querySelector('.wiz-qty-input');
      var qty = parseQty(qtyInput);

      if (!checkbox) return;
      if (checkbox.checked) {
        selectedCount += qty;
      } else if (row.getAttribute('data-source') === 'current-event') {
        returnCount += qty;
      }
    });

    var badge = document.getElementById('selected-badge');
    var footerCount = document.getElementById('footer-count');
    var sideCount = document.getElementById('selected-count');
    var sideReturn = document.getElementById('return-count');

    if (badge) badge.textContent = selectedCount + ' selected';
    if (footerCount) footerCount.textContent = selectedCount + ' item' + (selectedCount !== 1 ? 's' : '') + ' will transfer to new site';
    if (sideCount) sideCount.textContent = selectedCount + ' item' + (selectedCount !== 1 ? 's' : '');
    if (sideReturn) sideReturn.textContent = returnCount + ' item' + (returnCount !== 1 ? 's' : '') + ' return to warehouse';
  }

  function bindQuantityInputs() {
    document.querySelectorAll('.wiz-qty-input').forEach(function(input) {
      input.addEventListener('input', function() {
        if (parseQty(this) < 1) {
          this.value = 1;
        }
        updateCount();
      });
    });
  }

  // Toggle items from current event (with return logic)
  window.toggleCurrentEventItem = function(el, eventItemId) {
    el.classList.toggle('wiz-check-on');
    var cb = el.querySelector('.wiz-hidden-check');
    var returnCb = el.querySelector('.wiz-hidden-return-check');
    var row = el.closest('.wiz-item-row');
    var tag = row.querySelector('.transfer-tag');

    if (cb) {
      cb.checked = !cb.checked;

      // If unchecked, mark for return to warehouse
      if (returnCb) {
        returnCb.checked = !cb.checked;
      }

      // Update tag text
      if (tag) {
        if (cb.checked) {
          tag.textContent = 'Will transfer';
          tag.style.background = '#f0f7ff';
          tag.style.color = '#185FA5';
          tag.style.borderColor = '#dae8f7';
        } else {
          tag.textContent = 'Return to warehouse';
          tag.style.background = '#eaf3de';
          tag.style.color = '#3B6D11';
          tag.style.borderColor = '#d4e5c1';
        }
      }
    }

    updateCount();
  };

  // Toggle warehouse items (no return logic)
  window.toggleWarehouseItem = function(el) {
    el.classList.toggle('wiz-check-on');
    var cb = el.querySelector('.wiz-hidden-check');
    if (cb) {
      cb.checked = !cb.checked;
    }
    updateCount();
  };

  window.toggleCategory = function(el) {
    var catIndex = el.getAttribute('data-cat');
    var items = document.querySelectorAll('.wiz-item-check:not(.wiz-check-disabled)[data-cat="' + catIndex + '"]');
    var allOn = Array.from(items).every(function(i) { return i.classList.contains('wiz-check-on'); });

    items.forEach(function(item) {
      var cb = item.querySelector('.wiz-hidden-check');
      if (allOn) {
        item.classList.remove('wiz-check-on');
        if (cb) cb.checked = false;
      } else {
        item.classList.add('wiz-check-on');
        if (cb) cb.checked = true;
      }
    });

    el.classList.toggle('wiz-check-on', !allOn);
    updateCount();
  };

  var search = document.getElementById('checklist-search');
  if (search) {
    search.addEventListener('input', function() {
      var q = search.value.toLowerCase();
      document.querySelectorAll('.checklist-item').forEach(function(row) {
        var name = row.getAttribute('data-name') || '';
        row.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

  bindQuantityInputs();
  updateCount();

  // Prevent double form submission
  var form = document.getElementById('s2s-form');
  var submitButton = document.getElementById('s2s-submit');
  var isSubmitting = false;

  // Disable button clicks during submission
  if (submitButton) {
    submitButton.addEventListener('click', function(e) {
      if (isSubmitting) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    });
  }

  if (form) {
    form.addEventListener('submit', function(e) {
      if (isSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
      }

      // Check if at least 1 item is selected
      var checkedItems = document.querySelectorAll('.wiz-hidden-check:checked');
      if (checkedItems.length === 0) {
        e.preventDefault();
        alert('Please select at least one item to transfer to the new event.');
        return false;
      }

      // Mark as submitting IMMEDIATELY
      isSubmitting = true;

      // Disable submit button and show loading state
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation: spin 1s linear infinite"><circle cx="8" cy="8" r="6"/></svg> Creating Event...';
        submitButton.style.opacity = '0.6';
        submitButton.style.cursor = 'not-allowed';
        submitButton.style.pointerEvents = 'none';
      }

      // Prevent any other form submissions
      setTimeout(function() {
        if (form) {
          form.onsubmit = function() { return false; };
        }
      }, 100);
    }, {once: true}); // Use 'once' option to ensure handler only fires once
  }
})();
</script>

<style>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>

@endsection
