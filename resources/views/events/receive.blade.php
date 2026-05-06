@extends('layouts.app')
@section('title', 'Receive Items — ' . $event->name)
@section('page-title', 'Events')

@section('content')

@php
  $conditionLabels = [5=>'Excellent',4=>'Good',3=>'Fair',2=>'Average',1=>'Poor'];
  $total     = $eventItems->count() + $processedItems->count();
  $processed = $processedItems->count();
  $pending   = $eventItems->count();
  $pct       = $total > 0 ? round(($processed / $total) * 100) : 0;
@endphp

{{-- BREADCRUMB --}}
<div class="itd-breadcrumb">
  <a href="{{ route('events.index') }}" class="itd-bc-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    Events
  </a>
  <span class="itd-bc-sep">/</span>
  <a href="{{ route('events.show', $event) }}" class="itd-bc-link">{{ $event->name }}</a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">Receive Items</span>
</div>

{{-- FLASH --}}
@if(session('success'))
<div class="ev-flash ev-flash-success" style="margin-bottom:14px">
  <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
  {{ session('success') }}
</div>
@endif

{{-- PAGE HEADER --}}
<div class="rcv-header">
  <div class="rcv-header-left">
    <h1 class="rcv-title">Receive Items</h1>
    <p class="rcv-sub">{{ $event->name }} &middot; {{ $event->client_name }} &middot; {{ $event->venue }}</p>
  </div>
  <div class="rcv-header-right">
    <a href="{{ route('events.show', $event) }}" class="itd-btn-outline">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
      Back to Event
    </a>
  </div>
</div>

{{-- PROGRESS BAR --}}
<div class="rcv-progress-card">
  <div class="rcv-progress-top">
    <div class="rcv-progress-label">
      <span class="rcv-progress-title">Return Progress</span>
      <span class="rcv-progress-count">{{ $processed }} of {{ $total }} items received</span>
    </div>
    <span class="rcv-progress-pct">{{ $pct }}%</span>
  </div>
  <div class="rcv-progress-track">
    <div class="rcv-progress-fill" style="width:{{ $pct }}%"></div>
  </div>
  <div class="rcv-progress-stats">
    <span class="rcv-stat rcv-stat-pending">
      <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>
      {{ $pending }} pending
    </span>
    <span class="rcv-stat rcv-stat-warehouse">
      <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/></svg>
      {{ $processedItems->where('return_destination','warehouse')->count() }} to warehouse
    </span>
    <span class="rcv-stat rcv-stat-cleaning">
      <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><path d="M5 8l2 2 4-4"/></svg>
      {{ $processedItems->where('return_destination','cleaning')->count() }} to cleaning
    </span>
    <span class="rcv-stat rcv-stat-repair">
      <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2"/></svg>
      {{ $processedItems->where('return_destination','repair')->count() }} to repair
    </span>
  </div>
</div>

<div class="rcv-layout">

  {{-- LEFT: PENDING ITEMS FORM --}}
  <div class="rcv-main">

    @if($eventItems->count() > 0)
    <div class="rcv-section-head">
      <div class="rcv-section-title">Pending Return — {{ $pending }} items</div>
      <div class="rcv-section-sub">Set destination, condition and upload photos for each item</div>
    </div>

    <form method="POST" action="{{ route('events.receive.process', $event) }}" id="receive-form">
      @csrf

      <div class="rcv-items-list" id="rcv-items-list">
        @foreach($eventItems as $index => $eventItem)
        @php $item = $eventItem->item; @endphp
        <div class="rcv-item-card" id="rcv-card-{{ $eventItem->id }}">

          <input type="hidden" name="items[{{ $index }}][event_item_id]" value="{{ $eventItem->id }}">

          {{-- ITEM HEADER --}}
          <div class="rcv-item-head">
            <div class="rcv-item-thumb">
              @if($item && $item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name ?? '' }}">
              @else
                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="#c0b8b0" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
              @endif
            </div>
            <div class="rcv-item-info">
              <span class="rcv-item-name">{{ $item->name ?? 'Unknown item' }}</span>
              <span class="rcv-item-meta">{{ $item->category ?? '' }} &middot; #ITM-{{ str_pad($item->id ?? 0, 3, '0', STR_PAD_LEFT) }}</span>
              @if($eventItem->condition_on_dispatch)
                <span class="rcv-dispatch-cond">Dispatched in: {{ $conditionLabels[$eventItem->condition_on_dispatch] ?? '' }} condition</span>
              @endif
            </div>
            <div class="rcv-dest-selector" id="dest-{{ $eventItem->id }}">
              <button type="button" class="rcv-dest-btn rcv-dest-warehouse"
                      onclick="selectDest({{ $eventItem->id }}, 'warehouse', this)">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/><rect x="5" y="9" width="6" height="4"/></svg>
                Warehouse
              </button>
              <button type="button" class="rcv-dest-btn rcv-dest-cleaning"
                      onclick="selectDest({{ $eventItem->id }}, 'cleaning', this)">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 13c0-3 8-3 8 0M8 3v6"/></svg>
                Cleaning
              </button>
              <button type="button" class="rcv-dest-btn rcv-dest-repair"
                      onclick="selectDest({{ $eventItem->id }}, 'repair', this)">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2"/></svg>
                Repair
              </button>
              <input type="hidden" name="items[{{ $index }}][destination]"
                     id="dest-val-{{ $eventItem->id }}" value="" required>
            </div>
          </div>

          {{-- ITEM DETAILS --}}
          <div class="rcv-item-body">

            {{-- CONDITION --}}
            <div class="rcv-field">
              <label class="rcv-field-label">Return condition <span style="color:#CC0000">*</span></label>
              <div class="rcv-condition-row">
                @foreach([5=>'Excellent',4=>'Good',3=>'Fair',2=>'Average',1=>'Poor'] as $val => $label)
                <label class="rcv-cond-pill" id="cond-pill-{{ $eventItem->id }}-{{ $val }}">
                  <input type="radio" name="items[{{ $index }}][condition_on_return]"
                         value="{{ $val }}" style="display:none"
                         onchange="highlightCondition({{ $eventItem->id }}, {{ $val }})">
                  <span>{{ $val }} — {{ $label }}</span>
                </label>
                @endforeach
              </div>
            </div>

            {{-- NOTES --}}
            <div class="rcv-field">
              <label class="rcv-field-label">Return notes <span style="color:#a09890;font-weight:400">(optional)</span></label>
              <input type="text" name="items[{{ $index }}][return_notes]"
                     class="rcv-input" placeholder="Any notes about the item condition on return...">
            </div>

            {{-- PHOTO UPLOAD --}}
            <div class="rcv-field">
              <label class="rcv-field-label">Return photos <span style="color:#a09890;font-weight:400">(optional but recommended)</span></label>
              <div class="rcv-upload-row">
                <label class="rcv-upload-btn" style="cursor:pointer">
                  <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
                  Upload Photo
                  <input type="file" style="display:none" accept="image/jpeg,image/png,image/webp" multiple
                         data-event-item-id="{{ $eventItem->id }}"
                         data-upload-url="{{ route('events.receive.image', $event) }}"
                         data-csrf="{{ csrf_token() }}"
                         onchange="uploadReturnPhoto(this)">
                </label>
                <div class="rcv-photo-previews" id="photos-{{ $eventItem->id }}"></div>
              </div>
            </div>

          </div>
        </div>
        @endforeach
      </div>

      <div class="rcv-form-footer">
        <div class="rcv-footer-hint">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="7" x2="8" y2="11"/><circle cx="8" cy="5" r="0.5" fill="currentColor"/></svg>
          All items require a destination and condition before confirming
        </div>
        <button type="submit" class="rcv-confirm-btn" id="rcv-confirm-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
          Confirm Receipt of {{ $pending }} Items
        </button>
      </div>

    </form>

    @else
    <div class="rcv-all-done">
      <svg width="40" height="40" viewBox="0 0 16 16" fill="none" stroke="#3B6D11" stroke-width="1" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
      <p>All items have been received</p>
      <span>{{ $event->status === 'Completed' ? 'This event is now Completed.' : 'All dispatched items have been processed.' }}</span>
      <a href="{{ route('events.show', $event) }}" class="itd-btn-red" style="margin-top:12px">View Event</a>
    </div>
    @endif

  </div>

  {{-- RIGHT: ALREADY PROCESSED --}}
  <div class="rcv-side">

    @if($processedItems->count() > 0)
    <div class="evsh-side-card">
      <div class="evsh-side-card-title">Already Received ({{ $processedItems->count() }})</div>
      @foreach($processedItems as $pi)
      @php
        $destClass = match($pi->return_destination) {
          'warehouse' => 'rcv-done-warehouse',
          'cleaning'  => 'rcv-done-cleaning',
          'repair'    => 'rcv-done-repair',
          default     => '',
        };
        $destLabel = match($pi->return_destination) {
          'warehouse' => 'Warehouse',
          'cleaning'  => 'Cleaning',
          'repair'    => 'Repair',
          default     => '—',
        };
      @endphp
      <div class="rcv-done-item">
        <div class="rcv-done-name">{{ $pi->item->name ?? '—' }}</div>
        <div style="display:flex;align-items:center;gap:5px;margin-top:2px">
          <span class="rcv-done-badge {{ $destClass }}">{{ $destLabel }}</span>
          @if($pi->condition_on_return)
            <span style="font-size:9px;color:#a09890">{{ $pi->condition_on_return }}/5</span>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif

    <div class="evsh-side-card">
      <div class="evsh-side-card-title">Guide</div>
      <div style="display:flex;flex-direction:column;gap:10px">
        <div class="rcv-guide-item">
          <div class="rcv-guide-dot" style="background:#3B6D11"></div>
          <div>
            <p class="rcv-guide-title">Warehouse</p>
            <p class="rcv-guide-desc">Good condition, clean, ready to use again immediately</p>
          </div>
        </div>
        <div class="rcv-guide-item">
          <div class="rcv-guide-dot" style="background:#0F6E56"></div>
          <div>
            <p class="rcv-guide-title">Cleaning</p>
            <p class="rcv-guide-desc">Dirty but undamaged, needs cleaning before reuse</p>
          </div>
        </div>
        <div class="rcv-guide-item">
          <div class="rcv-guide-dot" style="background:#CC0000"></div>
          <div>
            <p class="rcv-guide-title">Repair</p>
            <p class="rcv-guide-desc">Damaged, broken or faulty — repair record created automatically</p>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<script>
(function() {

  function validateForm() {
    var cards = document.querySelectorAll('.rcv-item-card');
    var allValid = true;
    cards.forEach(function(card) {
      var id = card.id.replace('rcv-card-','');
      var dest = document.getElementById('dest-val-' + id);
      var cond = card.querySelector('input[type="radio"]:checked');
      if (!dest || !dest.value || !cond) allValid = false;
    });
    var btn = document.getElementById('rcv-confirm-btn');
    if (btn) {
      btn.disabled = !allValid;
      btn.style.opacity = allValid ? '1' : '0.5';
      btn.style.cursor  = allValid ? 'pointer' : 'not-allowed';
    }
  }

  window.selectDest = function(itemId, dest, btn) {
    var container = document.getElementById('dest-' + itemId);
    container.querySelectorAll('.rcv-dest-btn').forEach(function(b) {
      b.classList.remove('rcv-dest-active');
    });
    btn.classList.add('rcv-dest-active');
    var hidden = document.getElementById('dest-val-' + itemId);
    if (hidden) hidden.value = dest;

    var card = document.getElementById('rcv-card-' + itemId);
    if (card) {
      card.classList.remove('rcv-card-warehouse','rcv-card-cleaning','rcv-card-repair');
      card.classList.add('rcv-card-' + dest);
    }
    validateForm();
  };

  window.highlightCondition = function(itemId, val) {
    for (var i = 1; i <= 5; i++) {
      var pill = document.getElementById('cond-pill-' + itemId + '-' + i);
      if (pill) {
        pill.classList.remove('rcv-cond-active');
      }
    }
    var active = document.getElementById('cond-pill-' + itemId + '-' + val);
    if (active) active.classList.add('rcv-cond-active');
    validateForm();
  };

  window.uploadReturnPhoto = function(input) {
    var files   = Array.from(input.files);
    var itemId  = input.getAttribute('data-event-item-id');
    var url     = input.getAttribute('data-upload-url');
    var csrf    = input.getAttribute('data-csrf');
    var preview = document.getElementById('photos-' + itemId);
    if (!files.length || !preview) return;

    files.forEach(function(file) {
      var formData = new FormData();
      formData.append('image', file);
      formData.append('event_item_id', itemId);
      formData.append('_token', csrf);

      var placeholder = document.createElement('div');
      placeholder.className = 'rcv-photo-thumb rcv-photo-loading';
      placeholder.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#c0b8b0" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>';
      preview.appendChild(placeholder);

      fetch(url, { method:'POST', body:formData })
        .then(function(r){ return r.json(); })
        .then(function(data) {
          if (data.success) {
            placeholder.innerHTML = '<img src="' + data.url + '" alt="Return photo">';
            placeholder.className = 'rcv-photo-thumb';
          }
        })
        .catch(function() {
          placeholder.remove();
        });
    });
  };

  var form = document.getElementById('receive-form');
  var btn  = document.getElementById('rcv-confirm-btn');
  if (form && btn) {
    btn.disabled = true;
    btn.style.opacity = '0.5';
    btn.style.cursor  = 'not-allowed';
    form.addEventListener('submit', function() {
      btn.disabled = true;
      btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:wizSpin 0.8s linear infinite"><path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9"/></svg> Processing...';
    });
  }

})();
</script>

@endsection
