@extends('layouts.app')
@section('title', 'Create Event')
@section('page-title', 'Events')

@section('content')

{{-- PAGE HEADER --}}
<div class="wiz-page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <a href="{{ route('events.index') }}" class="wiz-back-link">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
        Events
      </a>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550;font-weight:500">Create Event</span>
    </div>
    <h1 class="wiz-page-title">Create New Event</h1>
  </div>
</div>

{{-- STEP INDICATOR --}}
<div class="wiz-stepper">
  <div class="wiz-step wiz-step-active">
    <div class="wiz-step-num">1</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Event Details</span>
      <span class="wiz-step-sub">Name, dates, location</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">2</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Packing List</span>
      <span class="wiz-step-sub">Items, borrowed & operational</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">3</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Assign Team</span>
      <span class="wiz-step-sub">Crew members</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">4</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Review & Confirm</span>
      <span class="wiz-step-sub">Summary & schedule</span>
    </div>
  </div>
</div>

<div class="wiz-layout">

  {{-- SIDEBAR --}}
  <div class="wiz-sidebar">
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Event summary</div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Event name</span>
        <span class="wiz-sum-val" id="sum-name">—</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Client</span>
        <span class="wiz-sum-val" id="sum-client">—</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Venue</span>
        <span class="wiz-sum-val" id="sum-venue">—</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Event date</span>
        <span class="wiz-sum-val" id="sum-date">—</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Location</span>
        <span class="wiz-sum-val" id="sum-location">—</span>
      </div>
    </div>
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Steps</div>
      <div class="wiz-step-list">
        <div class="wiz-step-li wiz-step-li-active">
          <div class="wiz-step-li-dot wiz-dot-active"></div>
          <span>Event details</span>
        </div>
        <div class="wiz-step-li wiz-step-li-inactive">
          <div class="wiz-step-li-dot"></div>
          <span>Packing list</span>
        </div>
        <div class="wiz-step-li wiz-step-li-inactive">
          <div class="wiz-step-li-dot"></div>
          <span>Assign team</span>
        </div>
        <div class="wiz-step-li wiz-step-li-inactive">
          <div class="wiz-step-li-dot"></div>
          <span>Review & confirm</span>
        </div>
      </div>
    </div>
  </div>

  {{-- MAIN FORM --}}
  <div class="wiz-main">
    <form method="POST" action="{{ route('events.store') }}" id="create-event-form">
      @csrf

      <div class="wiz-card">
        <div class="wiz-card-head">
          <div class="wiz-card-title">Event Details</div>
          <div class="wiz-card-sub">Fill in the core details for this event. Fields marked * are required.</div>
        </div>
        <div class="wiz-card-body">

          {{-- ERRORS --}}
          @if($errors->any())
          <div class="wiz-error-box">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg>
            <div>
              <p style="font-size:12px;font-weight:600;color:#7a0000;margin:0 0 4px">Please fix the following errors:</p>
              @foreach($errors->all() as $error)
                <p style="font-size:11px;color:#A32D2D;margin:0">{{ $error }}</p>
              @endforeach
            </div>
          </div>
          @endif

          {{-- SECTION: BASIC INFO --}}
          <div class="wiz-section-title">Basic Information</div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Event Name <span class="wiz-req">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}"
                     class="wiz-input {{ $errors->has('name') ? 'wiz-input-error' : '' }}"
                     placeholder="e.g. WRC Safari Rally Naivasha"
                     id="input-name" required>
              @error('name')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Client Name <span class="wiz-req">*</span></label>
              <input type="text" name="client_name" value="{{ old('client_name') }}"
                     class="wiz-input {{ $errors->has('client_name') ? 'wiz-input-error' : '' }}"
                     placeholder="e.g. Kenya Wildlife Service"
                     id="input-client" required>
              @error('client_name')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group wiz-full">
              <label class="wiz-label">Venue / Site Name <span class="wiz-req">*</span></label>
              <input type="text" name="venue" value="{{ old('venue') }}"
                     class="wiz-input {{ $errors->has('venue') ? 'wiz-input-error' : '' }}"
                     placeholder="e.g. Naivasha Sports Club, Hell's Gate"
                     id="input-venue" required>
              @error('venue')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
          </div>

          {{-- SECTION: DATES --}}
          <div class="wiz-section-title" style="margin-top:20px">Event Dates</div>
          <div class="wiz-date-info">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="7" x2="8" y2="11"/><circle cx="8" cy="5" r="0.5" fill="currentColor"/></svg>
            <span>Loading date is when items leave the warehouse. Setup date is when the venue is being prepared. Event date is the actual event. Set-down date is when items are packed up and returned.</span>
          </div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Loading Date <span class="wiz-req">*</span></label>
              <input type="date" name="loading_date" value="{{ old('loading_date') }}"
                     class="wiz-input {{ $errors->has('loading_date') ? 'wiz-input-error' : '' }}"
                     required>
              @error('loading_date')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Setup Date <span class="wiz-req">*</span></label>
              <input type="date" name="setup_date" value="{{ old('setup_date') }}"
                     class="wiz-input {{ $errors->has('setup_date') ? 'wiz-input-error' : '' }}"
                     required>
              @error('setup_date')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Event Date <span class="wiz-req">*</span></label>
              <input type="date" name="event_date" value="{{ old('event_date') }}"
                     class="wiz-input {{ $errors->has('event_date') ? 'wiz-input-error' : '' }}"
                     id="input-event-date" required>
              @error('event_date')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Set-down Date <span class="wiz-req">*</span></label>
              <input type="date" name="setdown_date" value="{{ old('setdown_date') }}"
                     class="wiz-input {{ $errors->has('setdown_date') ? 'wiz-input-error' : '' }}"
                     required>
              @error('setdown_date')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
          </div>

          {{-- SECTION: LOCATION --}}
          <div class="wiz-section-title" style="margin-top:20px">Location <span class="wiz-optional">(optional)</span></div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group wiz-full">
              <label class="wiz-label">Location Name</label>
              <input type="text" name="location_name" value="{{ old('location_name') }}"
                     class="wiz-input"
                     placeholder="e.g. Naivasha, Rift Valley, Nairobi"
                     id="input-location">
              <span class="wiz-field-hint">City, region, or area where the event is taking place</span>
            </div>
          </div>

          {{-- SECTION: ADDITIONAL --}}
          <div class="wiz-section-title" style="margin-top:20px">Billing & Customer <span class="wiz-optional">(optional)</span></div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Total Amount Due (KES)</label>
              <div class="wiz-input-prefix-wrap">
                <span class="wiz-input-prefix">KES</span>
                <input type="number" name="amount_due" value="{{ old('amount_due') }}"
                       class="wiz-input wiz-input-prefixed"
                       placeholder="0.00" step="0.01" min="0">
              </div>
              <span class="wiz-field-hint">The total amount the customer will pay via the portal</span>
              @error('amount_due')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Customer Phone (for M-Pesa)</label>
              <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                     class="wiz-input {{ $errors->has('customer_phone') ? 'wiz-input-error' : '' }}"
                     placeholder="e.g. 254700000000">
              <span class="wiz-field-hint">Used for Customer Portal login and M-Pesa push</span>
              @error('customer_phone')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Event Budget Cost (KES)</label>
              <div class="wiz-input-prefix-wrap">
                <span class="wiz-input-prefix">KES</span>
                <input type="number" name="cost" value="{{ old('cost') }}"
                       class="wiz-input wiz-input-prefixed"
                       placeholder="0.00" step="0.01" min="0">
              </div>
              <span class="wiz-field-hint">Internal operational cost for this event</span>
              @error('cost')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group wiz-full">
              <label class="wiz-label">Notes</label>
              <textarea name="notes" class="wiz-textarea"
                        placeholder="Any special instructions or notes about this event..."
                        rows="3">{{ old('notes') }}</textarea>
            </div>
          </div>

        </div>
        <div class="wiz-card-footer">
          <span class="wiz-footer-hint">Step 1 of 4 &mdash; Next: select items for this event</span>
          <div class="wiz-footer-actions">
            <a href="{{ route('events.index') }}" class="wiz-btn-cancel">Cancel</a>
            <button type="submit" class="wiz-btn-next" id="wiz-submit-btn">
              Save and Continue
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
  function sync(inputId, summaryId) {
    var el = document.getElementById(inputId);
    var out = document.getElementById(summaryId);
    if (!el || !out) return;
    el.addEventListener('input', function() {
      out.textContent = el.value || '—';
    });
  }
  sync('input-name',     'sum-name');
  sync('input-client',   'sum-client');
  sync('input-venue',    'sum-venue');
  sync('input-event-date', 'sum-date');
  sync('input-location', 'sum-location');

  var form = document.getElementById('create-event-form');
  var btn  = document.getElementById('wiz-submit-btn');
  if (form && btn) {
    form.addEventListener('submit', function() {
      btn.disabled = true;
      btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:wizSpin 0.8s linear infinite"><path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9"/></svg> Saving...';
      btn.style.opacity = '0.85';
    });
  }
})();
</script>

@endsection
