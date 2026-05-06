@extends('layouts.portal')
@section('title', 'Book an Event')

@section('content')
<div class="ptl-card" style="max-width: 600px; margin: 0 auto;">
  <div style="margin-bottom: 24px; text-align: center;">
    <h2 class="ptl-title" style="margin-bottom: 8px;">BOOK YOUR EVENT GEAR</h2>
    <p style="font-size: 13px; color: #5c5550; margin: 0;">Fill in the details below and our team will confirm availability and pricing.</p>
  </div>

  <form action="{{ route('portal.book.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
    @csrf
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div>
        <label class="ptl-label">Your Name</label>
        <input type="text" name="client_name" value="{{ old('client_name') }}" class="ptl-input" placeholder="Full Name" required>
      </div>
      <div>
        <label class="ptl-label">Phone Number</label>
        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="ptl-input" placeholder="2547XXXXXXXX" required>
      </div>
    </div>

    <div>
      <label class="ptl-label">Event / Occasion Name</label>
      <input type="text" name="name" value="{{ old('name') }}" class="ptl-input" placeholder="e.g. traditional Wedding, Corporate Launch" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div>
        <label class="ptl-label">Venue</label>
        <input type="text" name="venue" value="{{ old('venue') }}" class="ptl-input" placeholder="Event Location" required>
      </div>
      <div>
        <label class="ptl-label">Event Date</label>
        <input type="date" name="event_date" value="{{ old('event_date') }}" class="ptl-input" required>
      </div>
    </div>

    <div>
      <label class="ptl-label">What items do you need?</label>
      <textarea name="notes" rows="4" class="ptl-input" style="resize: vertical; min-height: 100px;" placeholder="Tell us what you need (e.g. 100 White Chairs, 2 Tents, Flooring...)" required>{{ old('notes') }}</textarea>
    </div>

    <button type="submit" class="ptl-btn-primary">
      Submit Booking Request
    </button>
  </form>

  <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f0ece8; text-align: center;">
    <p style="font-size: 13px; color: #a09890; margin-bottom: 12px;">Already have a booking?</p>
    <a href="{{ route('portal.login') }}" style="color: #CC0000; font-weight: 600; font-size: 14px; text-decoration: none;">Log in here</a>
  </div>
</div>
@endsection
