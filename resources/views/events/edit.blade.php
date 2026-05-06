@extends('layouts.app')
@section('title', 'Edit — ' . $event->name)
@section('page-title', 'Events')

@section('content')

<div class="wiz-page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <a href="{{ route('events.show', $event) }}" class="wiz-back-link">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
        {{ $event->name }}
      </a>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550;font-weight:500">Edit</span>
    </div>
    <h1 class="wiz-page-title">Edit Event</h1>
  </div>
</div>

<div class="wiz-layout">
  <div class="wiz-sidebar">
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Editing</div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Event</span><span class="wiz-sum-val">{{ $event->name }}</span></div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Status</span><span class="wiz-sum-val">{{ $event->status }}</span></div>
      <div class="wiz-summary-item"><span class="wiz-sum-label">Items</span><span class="wiz-sum-val">{{ $event->eventItems->count() }}</span></div>
    </div>
  </div>
  <div class="wiz-main">
    <form method="POST" action="{{ route('events.update', $event) }}">
      @csrf @method('PUT')
      <div class="wiz-card">
        <div class="wiz-card-head">
          <div class="wiz-card-title">Event Details</div>
          <div class="wiz-card-sub">Update the details for this event.</div>
        </div>
        <div class="wiz-card-body">

          @if($errors->any())
          <div class="wiz-error-box">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg>
            <div>
              @foreach($errors->all() as $error)
                <p style="font-size:11px;color:#A32D2D;margin:0">{{ $error }}</p>
              @endforeach
            </div>
          </div>
          @endif

          <div class="wiz-section-title">Basic Information</div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Event Name <span class="wiz-req">*</span></label>
              <input type="text" name="name" value="{{ old('name', $event->name) }}" class="wiz-input" required>
              @error('name')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Client Name <span class="wiz-req">*</span></label>
              <input type="text" name="client_name" value="{{ old('client_name', $event->client_name) }}" class="wiz-input" required>
              @error('client_name')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="wiz-form-group wiz-full">
              <label class="wiz-label">Venue <span class="wiz-req">*</span></label>
              <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" class="wiz-input" required>
              @error('venue')<span class="wiz-field-error">{{ $message }}</span>@enderror
            </div>
          </div>

          <div class="wiz-section-title" style="margin-top:20px">Event Dates</div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Loading Date <span class="wiz-req">*</span></label>
              <input type="date" name="loading_date" value="{{ old('loading_date', $event->loading_date->format('Y-m-d')) }}" class="wiz-input" required>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Setup Date <span class="wiz-req">*</span></label>
              <input type="date" name="setup_date" value="{{ old('setup_date', $event->setup_date->format('Y-m-d')) }}" class="wiz-input" required>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Event Date <span class="wiz-req">*</span></label>
              <input type="date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" class="wiz-input" required>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Set-down Date <span class="wiz-req">*</span></label>
              <input type="date" name="setdown_date" value="{{ old('setdown_date', $event->setdown_date->format('Y-m-d')) }}" class="wiz-input" required>
            </div>
          </div>

          <div class="wiz-section-title" style="margin-top:20px">Location <span class="wiz-optional">(optional)</span></div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group wiz-full">
              <label class="wiz-label">Location Name</label>
              <input type="text" name="location_name" value="{{ old('location_name', $event->location_name) }}" class="wiz-input" placeholder="e.g. Naivasha, Rift Valley">
            </div>
          </div>

          <div class="wiz-section-title" style="margin-top:20px">Billing & Customer <span class="wiz-optional">(optional)</span></div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Total Amount Due (KES)</label>
              <div class="wiz-input-prefix-wrap">
                <span class="wiz-input-prefix">KES</span>
                <input type="number" name="amount_due" value="{{ old('amount_due', $event->amount_due) }}" class="wiz-input wiz-input-prefixed" placeholder="0.00" step="0.01" min="0">
              </div>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Customer Phone (for M-Pesa)</label>
              <input type="text" name="customer_phone" value="{{ old('customer_phone', $event->customer_phone) }}" class="wiz-input" placeholder="e.g. 254700000000">
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Event Budget Cost (KES)</label>
              <div class="wiz-input-prefix-wrap">
                <span class="wiz-input-prefix">KES</span>
                <input type="number" name="cost" value="{{ old('cost', $event->cost) }}" class="wiz-input wiz-input-prefixed" placeholder="0.00" step="0.01" min="0">
              </div>
            </div>
            <div class="wiz-form-group wiz-full">
              <label class="wiz-label">Notes</label>
              <textarea name="notes" class="wiz-textarea" rows="3">{{ old('notes', $event->notes) }}</textarea>
            </div>
          </div>
        </div>
        <div class="wiz-card-footer">
          <span class="wiz-footer-hint">Changes will be saved immediately</span>
          <div class="wiz-footer-actions">
            <a href="{{ route('events.show', $event) }}" class="wiz-btn-cancel">Cancel</a>
            <button type="submit" class="wiz-btn-next">
              Save Changes
              <svg width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
