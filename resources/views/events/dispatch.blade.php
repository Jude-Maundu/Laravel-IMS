@extends('layouts.app')
@section('title', 'Dispatch — ' . $event->name)
@section('page-title', 'Events')

@section('content')

<div class="wiz-page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <a href="{{ route('events.index') }}" class="wiz-back-link">Events</a>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550">{{ $event->name }}</span>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550;font-weight:500">Dispatch</span>
    </div>
    <h1 class="wiz-page-title">Dispatch Items</h1>
  </div>
</div>

<div class="wiz-stepper">
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg></div>
    <div class="wiz-step-info"><span class="wiz-step-label" style="color:#3B6D11">Event Details</span><span class="wiz-step-sub">Done</span></div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg></div>
    <div class="wiz-step-info"><span class="wiz-step-label" style="color:#3B6D11">Item Checklist</span><span class="wiz-step-sub">{{ $eventItems->count() }} items</span></div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg></div>
    <div class="wiz-step-info"><span class="wiz-step-label" style="color:#3B6D11">Team</span><span class="wiz-step-sub">Assigned</span></div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-active">
    <div class="wiz-step-num">4</div>
    <div class="wiz-step-info"><span class="wiz-step-label">Dispatch</span><span class="wiz-step-sub">Condition + photos</span></div>
  </div>
</div>

<div class="wiz-layout">

  <div class="wiz-sidebar">
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Dispatching to</div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Event</span><span class="wiz-sum-val">{{ $event->name }}</span></div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Client</span><span class="wiz-sum-val">{{ $event->client_name }}</span></div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Date</span><span class="wiz-sum-val">{{ $event->event_date->format('d M Y') }}</span></div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Venue</span><span class="wiz-sum-val">{{ $event->venue }}</span></div>
    </div>
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Items</div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Total items</span><span class="wiz-sum-val" style="color:#CC0000;font-weight:700">{{ $eventItems->count() }}</span></div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Conditions filled</span><span class="wiz-sum-val" id="conditions-done">0 / {{ $eventItems->count() }}</span></div>
    </div>
    <div class="wiz-sidebar-card" style="background:#fff8f8;border-color:#f5c0c0">
      <div class="wiz-sidebar-title" style="color:#CC0000">Required</div>
      <p style="font-size:11px;color:#7a0000;line-height:1.5">All items must have a condition rating before dispatch. Photos are optional but strongly recommended.</p>
    </div>
  </div>

  <div class="wiz-main">
    <form method="POST" action="{{ route('events.dispatch.confirm', $event) }}" id="dispatch-form">
      @csrf

      <div style="display:flex;flex-direction:column;gap:10px">

        @foreach($eventItems as $index => $eventItem)
        <div class="wiz-dispatch-item" id="dispatch-item-{{ $eventItem->id }}">
          <div class="wiz-di-head">
            <div class="wiz-di-check">
              <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
            </div>
            <div class="wiz-di-thumb">
              @if($eventItem->item->image_path)
                <img src="{{ asset('storage/' . $eventItem->item->image_path) }}" alt="{{ $eventItem->item->name }}">
              @else
                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="#c0b8b0" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
              @endif
            </div>
            <div class="wiz-di-info">
              <span class="wiz-di-name">{{ $eventItem->item->name }}</span>
              <span class="wiz-di-cat">{{ $eventItem->item->category }} &middot; #ITM-{{ str_pad($eventItem->item->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <span class="wiz-di-req" id="req-{{ $eventItem->id }}">Condition required</span>
          </div>
          <div class="wiz-di-body">
            <input type="hidden" name="items[{{ $index }}][event_item_id]" value="{{ $eventItem->id }}">
            <div class="wiz-di-field">
              <label class="wiz-di-field-label">Condition on dispatch <span style="color:#CC0000">*</span></label>
              <select name="items[{{ $index }}][condition]"
                      class="wiz-di-select"
                      onchange="conditionChanged({{ $eventItem->id }}, this.value)"
                      required>
                <option value="">Select condition...</option>
                <option value="5">5 — Excellent</option>
                <option value="4">4 — Good</option>
                <option value="3">3 — Fair</option>
                <option value="2">2 — Average</option>
                <option value="1">1 — Poor</option>
              </select>
            </div>
            <div class="wiz-di-field">
              <label class="wiz-di-field-label">Dispatch notes (optional)</label>
              <input type="text" name="items[{{ $index }}][notes]"
                     class="wiz-di-input"
                     placeholder="Any notes about this item's condition...">
            </div>
            <div class="wiz-di-upload-area"
                 onclick="document.getElementById('img-{{ $eventItem->id }}').click()">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#c0b8b0" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
              <span>Click to upload pre-dispatch photo(s)</span>
              <span class="wiz-upload-sub">JPEG, PNG or WEBP &mdash; max 4MB each</span>
              <input type="file" id="img-{{ $eventItem->id }}"
                     accept="image/jpeg,image/png,image/webp"
                     multiple
                     style="display:none"
                     onchange="previewImages(this, {{ $eventItem->id }}, '{{ route('events.dispatch.image', $event) }}')">
            </div>
            <div class="wiz-img-preview-row" id="preview-{{ $eventItem->id }}"></div>
          </div>
        </div>
        @endforeach

      </div>

      <div class="wiz-card-footer" style="margin-top:12px;background:#fff;border:1px solid #ece8e3;border-radius:10px">
        <span class="wiz-footer-hint">All condition ratings are required before dispatching</span>
        <div class="wiz-footer-actions">
          <a href="{{ route('events.checklist', $event) }}" class="wiz-btn-cancel">← Back</a>
          <button type="submit" class="wiz-btn-dispatch" id="dispatch-submit">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/><rect x="6" y="9" width="4" height="4"/></svg>
            Confirm &amp; Dispatch {{ $eventItems->count() }} Items
          </button>
        </div>
      </div>

    </form>
  </div>

</div>

<script>
(function() {
  var total = {{ $eventItems->count() }};
  var filled = 0;

  window.conditionChanged = function(itemId, val) {
    var req = document.getElementById('req-' + itemId);
    if (val) {
      if (req) { req.textContent = 'Condition set'; req.style.color = '#3B6D11'; }
      filled++;
    } else {
      if (req) { req.textContent = 'Condition required'; req.style.color = '#a09890'; }
      filled--;
    }
    var done = document.getElementById('conditions-done');
    if (done) done.textContent = Math.max(0, filled) + ' / ' + total;
  };

  window.previewImages = function(input, itemId, uploadUrl) {
    var preview = document.getElementById('preview-' + itemId);
    if (!preview) return;
    Array.from(input.files).forEach(function(file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'wiz-preview-img';
        img.title = file.name;
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);

      var formData = new FormData();
      formData.append('image', file);
      formData.append('event_item_id', itemId);
      formData.append('type', 'dispatch');
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      fetch(uploadUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .catch(function(err) { console.error('Upload failed', err); });
    });
  };

  var form = document.getElementById('dispatch-form');
  var btn  = document.getElementById('dispatch-submit');
  if (form && btn) {
    form.addEventListener('submit', function() {
      btn.disabled = true;
      btn.textContent = 'Dispatching...';
      btn.style.opacity = '0.85';
    });
  }
})();
</script>

@endsection
